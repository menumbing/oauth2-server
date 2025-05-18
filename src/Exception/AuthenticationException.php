<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Exception;

use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthenticationException extends OAuthServerException
{
    public static function missingScope(array $scopes): static
    {
        return new static(
            message: sprintf('Missing scope "%s" provided.', implode(', ', $scopes)),
            code: 403,
            errorType: 'invalid_scope',
            hint: 'Make sure your access token request includes the "scope" parameter with the required scopes.',
        );
    }

    public static function forbiddenToUseScope(string $scope): static
    {
        return new static(
            message: sprintf('This client is forbidden to use scope "%s".', $scope),
            code: 403,
            errorType: 'invalid_scope',
            hint: 'Make sure your are allowed to use this scope.',
        );
    }
}
