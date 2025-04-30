<?php

use League\OAuth2\Server\Grant;

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

    // Repositories - You need to create these classes
    'repositories' => [
        'client' => null,
        'access_token' => null,
        'scope' => null,
        'auth_code' => null,
        'refresh_token' => null,
        'user' => null, // Required for Password Grant & Auth Code Grant
    ],

    'grant_types' => [
        // Enable Password Grant (Requires UserRepository)
        Grant\PasswordGrant::class => [
            'repository' => 'user', // Reference to the user repository key above
            //'callback' => [\App\OAuth\Grants\PasswordGrantVerifier::class, 'verify'], // Custom verifier logic
            'refresh_token_repository' => 'refresh_token', // Reference to the refresh token repository
        ],
        // Enable Client Credentials Grant
        Grant\ClientCredentialsGrant::class => [],
        // Enable Authorization Code Grant (Requires AuthCodeRepository, UserRepository)
        Grant\AuthCodeGrant::class => [
            'repository' => 'auth_code', // Reference to the auth code repository
            'refresh_token_repository' => 'refresh_token', // Reference to the refresh token repository
        ],
        // Enable Refresh Token Grant
        Grant\RefreshTokenGrant::class => [
            'repository' => 'refresh_token', // Reference to the refresh token repository
        ],
        // Enable Implicit Grant (Not Recommended for Confidential Clients)
        Grant\ImplicitGrant::class => [
            'access_token_expire_in' => new \DateInterval('PT1H'),
        ],
    ],

    // Define available scopes
    'scopes' => [],
];
