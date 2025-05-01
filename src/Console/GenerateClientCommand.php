<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Console;

use Hyperf\Command\Command;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Stringable\Str;
use Menumbing\OAuth2\Server\Contract\ClientModelInterface;
use Menumbing\OAuth2\Server\Contract\ClientModelRepositoryInterface;
use Psr\Container\ContainerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class GenerateClientCommand extends Command
{
    protected ?string $signature = 'gen:oauth2-client
            {--password : Create a password grant client}
            {--client : Create a client credentials grant client}
            {--name= : The name of the client}
            {--redirect= : The redirect URI of the client}
            {--user= : The user associated with the client}
            {--public : Create a public client}';

    protected string $description = 'Create a client for issuing access tokens';

    protected ConfigInterface $config;
    protected ClientModelRepositoryInterface $clientModelRepository;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct();

        $this->config = $this->container->get(ConfigInterface::class);

        $this->clientModelRepository = $this->container->get(
            $this->config->get('oauth2-server.repositories.client')
        );
    }

    public function handle(): void
    {
        if ($this->input->getOption('password')) {
            $this->createPasswordGrantClient();

            return;
        }

        if ($this->input->getOption('client')) {
            $this->createClientCredentialsClient();

            return;
        }

        $this->createAuthCodeClient();
    }

    protected function createPasswordGrantClient(): void
    {
        $client = $this->clientModelRepository->createPasswordGrantClient(
            null, $this->askClientName(), 'http://localhost'
        );

        $this->info('Password grant client created successfully.');

        $this->outputClientDetails($client);
    }

    protected function createClientCredentialsClient(): void
    {
        $client = $this->clientModelRepository->create(
            null, $this->askClientName(), ''
        );

        $this->info('New client created successfully.');

        $this->outputClientDetails($client);
    }

    protected function createAuthCodeClient(): void
    {
        $userId = $this->askForUserId();
        $name = $this->askClientName();
        $redirect = $this->askForRedirect();

        $client = $this->clientModelRepository->create(
            $userId, $name, $redirect, false, false, !$this->input->getOption('public')
        );

        $this->info('New client created successfully.');

        $this->outputClientDetails($client);
    }

    protected function askClientName(): string
    {
        return $this->input->getOption('name') ?: $this->ask(
            'What should we name the personal access client?',
            $this->config->get('app_name') . ' Personal Access Client'
        );
    }

    protected function askForRedirect(): string
    {
        return $this->input->getOption('redirect') ?: $this->ask(
            'Where should we redirect the request after authorization?',
            $this->genUrl('/auth/callback')
        );
    }

    protected function askForUserId(): string
    {
        return $this->input->getOption('user') ?: $this->ask(
            'Which user ID should the client be assigned to?'
        );
    }

    protected function outputClientDetails(ClientModelInterface $client): void
    {
        if ($this->config->get('oauth2-server.hashes_client_secret')) {
            $this->line('<comment>Here is your new client secret. This is the only time it will be shown so don\'t lose it!</comment>');
            $this->line('');
        }

        $this->line('<comment>Client ID:</comment> ' . $client->getIdentifier());
        $this->line('<comment>Client secret:</comment> ' . $client->getPlainSecret());;
    }

    protected function genUrl(string $toUrl): string
    {
        return 'http://localhost' . (Str::startsWith($toUrl, '/') ? $toUrl : '/' . $toUrl);
    }
}
