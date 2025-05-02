<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Menumbing\OAuth2\Server;

use Hyperf\Contract\ConfigInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use Menumbing\OAuth2\Server\Bridge\Repository\AccessTokenRepository;
use Menumbing\OAuth2\Server\Bridge\Repository\AuthCodeRepository;
use Menumbing\OAuth2\Server\Bridge\Repository\ClientRepository;
use Menumbing\OAuth2\Server\Bridge\Repository\RefreshTokenRepository;
use Menumbing\OAuth2\Server\Bridge\Repository\ScopeRepository;
use Menumbing\OAuth2\Server\Bridge\Repository\UserRepository;
use Menumbing\OAuth2\Server\Console\GenerateClientCommand;
use Menumbing\OAuth2\Server\Console\PurgeTokenCommand;
use Menumbing\OAuth2\Server\Contract\AuthorizeRequestInterface;
use Menumbing\OAuth2\Server\Contract\TokenIssuerInterface;
use Menumbing\OAuth2\Server\Factory\AuthorizationServerFactory;
use Menumbing\OAuth2\Server\Factory\ResourceServerFactory;
use Menumbing\OAuth2\Server\Listener\RegisterRoutesListener;
use Menumbing\Signature\Contract\ClientRepositoryInterface;
use Psr\Container\ContainerInterface;

use function Hyperf\Support\make;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                AccessTokenRepositoryInterface::class => $this->repositoryFactory('access_token'),
                ClientRepositoryInterface::class => $this->repositoryFactory('client'),
                AuthCodeRepositoryInterface::class => $this->repositoryFactory('auth_code'),
                UserRepositoryInterface::class => $this->repositoryFactory('user'),
                RefreshTokenRepositoryInterface::class => $this->repositoryFactory('refresh_token'),
                ScopeRepositoryInterface::class => ScopeRepository::class,

                TokenIssuerInterface::class => TokenIssuer::class,
                AuthorizeRequestInterface::class => AuthorizeRequest::class,

                ResourceServer::class => ResourceServerFactory::class,
                AuthorizationServer::class => AuthorizationServerFactory::class,
            ],
            'listeners' => [
                RegisterRoutesListener::class => -1,
            ],
            'commands' => [
                GenerateClientCommand::class,
                PurgeTokenCommand::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for oauth2-server.',
                    'source' => __DIR__ . '/../publish/oauth2-server.php',
                    'destination' => BASE_PATH . '/config/autoload/oauth2-server.php',
                ],
                [
                    'id' => 'migration',
                    'description' => 'The migration for oauth2-server.',
                    'source' => __DIR__ . '/../publish/migrations/2025_04_27_171004_create_users_table.php',
                    'destination' => BASE_PATH . '/migrations/2025_04_27_171004_create_users_table.php',
                ],
                [
                    'id' => 'migration',
                    'description' => 'The migration for oauth2-server.',
                    'source' => __DIR__ . '/../publish/migrations/2025_04_27_172331_create_oauth_clients_table.php',
                    'destination' => BASE_PATH . '/migrations/2025_04_27_172331_create_oauth_clients_table.php',
                ],
                [
                    'id' => 'migration',
                    'description' => 'The migration for oauth2-server.',
                    'source' => __DIR__ . '/../publish/migrations/2025_04_27_232221_create_oauth_auth_codes_table.php',
                    'destination' => BASE_PATH . '/migrations/2025_04_27_232221_create_oauth_auth_codes_table.php',
                ],
                [
                    'id' => 'migration',
                    'description' => 'The migration for oauth2-server.',
                    'source' => __DIR__ . '/../publish/migrations/2025_04_30_124841_create_oauth_access_tokens_table.php',
                    'destination' => BASE_PATH . '/migrations/2025_04_30_124841_create_oauth_access_tokens_table.php',
                ],
                [
                    'id' => 'migration',
                    'description' => 'The migration for oauth2-server.',
                    'source' => __DIR__ . '/../publish/migrations/2025_04_30_125045_create_oauth_refresh_tokens_table.php',
                    'destination' => BASE_PATH . '/migrations/2025_04_30_125045_create_oauth_refresh_tokens_table.php',
                ],
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }

    protected function repositoryFactory(string $type): callable
    {
        return function (ContainerInterface $container) use ($type) {
            $mapping = [
                'client' => ClientRepository::class,
                'user' => UserRepository::class,
                'auth_code' => AuthCodeRepository::class,
                'access_token' => AccessTokenRepository::class,
                'refresh_token' => RefreshTokenRepository::class,
            ];

            $config = $container->get(ConfigInterface::class);
            $repositoryClass = $config->get('oauth2-server.repositories.' . $type);

            return make(
                $mapping[$type],
                ['modelRepository' => $container->get($repositoryClass)]
            );
        };
    }
}
