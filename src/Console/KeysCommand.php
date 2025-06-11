<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Console;

use Hyperf\Collection\Arr;
use Hyperf\Command\Command;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Stringable\Str;
use phpseclib\Crypt\RSA as LegacyRSA;
use phpseclib3\Crypt\RSA;
use Psr\Container\ContainerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class KeysCommand extends Command
{
    protected ?string $signature = 'gen:oauth2-keys
            {--force : Force overwrite existing keys}
            {--length=4096 : Length of the private key}';

    protected string $description = 'Create the encryption keys for OAuth2';

    protected ConfigInterface $config;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct();

        $this->config = $container->get(ConfigInterface::class);
    }

    public function handle(): void
    {
        [$publicKey, $privateKey] = [
            $this->config->get('oauth2_server.public_key') ?? 'runtime/oauth-public.key',
            $this->config->get('oauth2_server.private_key') ?? 'runtime/oauth-private.key',
        ];

        if ((file_exists($publicKey) || file_exists($privateKey)) && !$this->input->getOption('force')) {
            $this->error('Encryption keys already exist. Use the --force option to overwrite them.');

            return;
        }

        if (class_exists(LegacyRSA::class)) {
            $keys = (new LegacyRSA)->createKey($this->input ? (int)$this->input->getOption('length') : 4096);

            file_put_contents($publicKey, Arr::get($keys, 'publickey'));
            file_put_contents($privateKey, Arr::get($keys, 'privatekey'));
        } else {
            $key = RSA::createKey($this->input ? (int)$this->input->getOption('length') : 4096);

            file_put_contents($publicKey, (string) $key->getPublicKey());
            file_put_contents($privateKey, (string) $key);
        }

        $encryptionKey = Str::random(50);

        $this->writeEnvironmentFile('OAUTH2_PRIVATE_KEY', 'oauth2_server.private_key', $privateKey);;
        $this->writeEnvironmentFile('OAUTH2_PUBLIC_KEY', 'oauth2_server.public_key', $publicKey);;
        $this->writeEnvironmentFile('OAUTH2_ENCRYPTION_KEY', 'oauth2_server.encryption_key', $encryptionKey);

        $this->info('Encryption keys generated successfully and set in your .env file.');
    }

    protected function writeEnvironmentFile(string $env, string $configKey, string $value): void
    {
        $input = file_get_contents($this->getEnvFile());

        if (preg_match($this->keyReplacement($env, $configKey), $input)) {
            $replaced = $this->replaceEnvironment($input, $env, $configKey, $value);
        } else {
            $replaced = $input . "\n" . $env . '=' . $value;
        }

        file_put_contents($this->getEnvFile(), $replaced);
    }

    protected function replaceEnvironment(string $input, string $env, string $configKey, string $value): string
    {
        return preg_replace(
            $this->keyReplacement($env, $configKey),
            sprintf('%s=%s', $env, $value),
            $input
        );
    }

    protected function keyReplacement(string $env, string $configKey): string
    {
        $escaped = preg_quote('=' . $this->config->get($configKey), '/');

        return "/^{$env}{$escaped}/m";
    }

    protected function getEnvFile(): string
    {
        return BASE_PATH . '/.env';
    }
}
