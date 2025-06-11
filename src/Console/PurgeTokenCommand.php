<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Console;

use Hyperf\Command\Command;
use Hyperf\Contract\ConfigInterface;
use Menumbing\OAuth2\Server\Contract\AuthCodeModelRepositoryInterface;
use Menumbing\OAuth2\Server\Contract\RefreshTokenModelRepositoryInterface;
use Menumbing\OAuth2\Server\Contract\TokenModelRepositoryInterface;
use Menumbing\OAuth2\Server\Repository\AccessTokenModelRepository;
use Menumbing\OAuth2\Server\Repository\AuthCodeModelRepository;
use Menumbing\OAuth2\Server\Repository\RefreshTokenModelRepository;
use Psr\Container\ContainerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class PurgeTokenCommand extends Command
{
    protected ?string $signature = 'purge:oauth2-tokens';

    protected string $description = 'Purge expired and revoked OAuth2 tokens and code.';

    protected ConfigInterface $config;

    protected TokenModelRepositoryInterface $accessTokenRepository;

    protected RefreshTokenModelRepositoryInterface $refreshTokenRepository;

    protected AuthCodeModelRepositoryInterface $authCodeRepository;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct();

        $this->config = $container->get(ConfigInterface::class);
        $this->accessTokenRepository = $this->getRepository('access_token', AccessTokenModelRepository::class);
        $this->refreshTokenRepository = $this->getRepository('refresh_token', RefreshTokenModelRepository::class);
        $this->authCodeRepository = $this->getRepository('auth_code', AuthCodeModelRepository::class);
    }

    public function handle(): void
    {
        $this->refreshTokenRepository->purgeAllRevokedAndExpired();
        $this->accessTokenRepository->purgeAllRevokedAndExpired();
        $this->authCodeRepository->purgeAllRevokedAndExpired();

        $this->info('All revoked and expired token that more than 7 days have been purged.');
    }

    protected function getRepository(string $type, string $default): mixed
    {
        return $this->container->get(
            $this->config->get(sprintf('oauth2_server.repositories.%s', $type), $default)
        );
    }
}
