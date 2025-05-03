<?php

use League\OAuth2\Server\Grant;
use Menumbing\OAuth2\Server\Http\Controller\GetAccessTokenInfoController;
use Menumbing\OAuth2\Server\Http\Controller\GetScopeListController;
use Menumbing\OAuth2\Server\Http\Controller\GetUserInfoController;
use Menumbing\OAuth2\Server\Http\Controller\IssueTokenController;
use Menumbing\OAuth2\Server\Http\Middleware\CheckClientCredentialsForAnyScope;
use Menumbing\OAuth2\Server\Repository;

use function Hyperf\Support\env;

return [
    // Private key path or content
    'private_key' => env('OAUTH2_PRIVATE_KEY'),
    // Public key path or content
    'public_key' => env('OAUTH2_PUBLIC_KEY'),
    // Encryption key
    'encryption_key' => env('OAUTH2_ENCRYPTION_KEY'), // Use a secure key, ideally from .env
    // Client secret in the database should be hashes or not
    'hashes_client_secret' => env('OAUTH2_HASHES_CLIENT_SECRET', false),

    // Find a user with a specific key
    'user_find_by' => 'email',

    // Access token lifetime (e.g., 1 hour)
    'access_token_expire_in' => new \DateInterval('PT1H'),
    // Refresh token lifetime (e.g., 1 month)
    'refresh_token_expire_in' => new \DateInterval('P1M'),
    // Auth code lifetime (e.g., 10 minutes)
    'auth_code_expire_in' => new \DateInterval('PT10M'),

    // Custom response type that should implement ResponseTypeInterface
    'response_type' => null,

    'routes' => [
        'issue_token' => [
            'server' => 'http',
            'path' => '/oauth/token',
            'handler' => [IssueTokenController::class, 'issueToken'],
            'options' => [
                'middleware' => [],
            ],
        ],
        'user_info' => [
            'server' => 'http',
            'path' => '/oauth/me',
            'handler' => [GetUserInfoController::class, 'infoMe'],
            'options' => [
                'middleware' => [],
                'guard' => 'oauth2',
            ]
        ],
        'scope_list' => [
            'server' => 'http',
            'path' => '/oauth/scopes',
            'handler' => [GetScopeListController::class, 'index'],
            'options' => [
                'middleware' => [],
                'guard' => 'oauth2',
            ]
        ],
        'token_validity' => [
            'server' => 'http',
            'path' => '/oauth/tokens/{tokenId}/validity',
            'handler' => [GetAccessTokenInfoController::class, 'isRevoked'],
            'options' => [
                'middleware' => [CheckClientCredentialsForAnyScope::class],
                'scope' => 'check-token-validity',
            ]
        ]
    ],

    // Database connection for storage of auth code, access token and refresh token
    'database' => [
        'connection' => env('OAUTH2_DB_CONNECTION', 'default'),
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
    'scopes' => [
        'basic' => 'Basic user information.',
        'email' => 'User email address.',
        'check-token-validity' => 'Check if the access token is revoked.',
    ],

    // Define user info fields based on scope
    'user_info_fields' => [
        'basic' => 'name, created_at, updated_at',
        'email' => 'email',
    ]
];
