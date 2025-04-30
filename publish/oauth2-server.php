<?php

use League\OAuth2\Server\Grant;
use Menumbing\OAuth2\Server\Repository;

use function Hyperf\Support\env;

return [
    // Private key path or content
    'private_key' => BASE_PATH . '/storage/oauth/private.key',
    // Encryption key
    'encryption_key' => env('OAUTH_ENCRYPTION_KEY', 'YourSecretEncryptionKey'), // Use a secure key, ideally from .env
    // Client secret in the database should be hashes or not
    'hashes_client_secret' => env('OAUTH_HASHES_CLIENT_SECRET', false),

    // Find a user with a specific key
    'user_find_by' => env('OAUTH_USER_FIND_BY', 'email'),

    // Access token lifetime (e.g., 1 hour)
    'access_token_expire_in' => new \DateInterval('PT1H'),
    // Refresh token lifetime (e.g., 1 month)
    'refresh_token_expire_in' => new \DateInterval('P1M'),
    // Auth code lifetime (e.g., 10 minutes)
    'auth_code_expire_in' => new \DateInterval('PT10M'),

    // Database connection for storage of auth code, access token and refresh token
    'database' => [
        'connection' => env('OAUTH_DB_CONNECTION', 'default'),
    ],

    'repositories' => [
        'client' => Repository\ClientModelRepository::class,
        'user' => Repository\UserModelRepository::class,
        'auth_code' => Repository\AuthCodeModelRepository::class,
        'access_token' => Repository\AccessTokenModelRepository::class,
        'refresh_token' => Repository\RefreshTokenModelRepository::class,
    ],

    'grant_types' => [
        // Enable Password Grant (Requires UserRepository)
        Grant\PasswordGrant::class,
        // Enable Client Credentials Grant
        Grant\ClientCredentialsGrant::class,
        // Enable Authorization Code Grant (Requires AuthCodeRepository, UserRepository)
        Grant\AuthCodeGrant::class,
        // Enable Refresh Token Grant
        Grant\RefreshTokenGrant::class,
        // Enable Implicit Grant (Not Recommended for Confidential Clients)
        Grant\ImplicitGrant::class,
    ],

    // Define available scopes
    'scopes' => [],
];
