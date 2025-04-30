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

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
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
}
