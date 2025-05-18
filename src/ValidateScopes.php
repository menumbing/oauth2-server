<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server;

use Hyperf\HttpServer\Router\Dispatched;
use League\OAuth2\Server\ResourceServer;
use Menumbing\OAuth2\Server\Exception\AuthenticationException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait ValidateScopes
{
    protected ResourceServer $server;

    protected ?string $clientId = null;

    protected ?string $userId = null;

    protected array $tokenScopes = [];

    protected function validateScopes(ServerRequestInterface $request): ?ServerRequestInterface
    {
        $request = $this->validateRequest($request);
        $scopes = (array) $this->getOption($request, 'scope', []);
        $tokenScopes = $this->tokenScopes;

        if (empty($scopes)) {
            return $request;
        }

        if (in_array('*', $tokenScopes, true)) {
            return $request;
        }

        foreach ($tokenScopes as $scope) {
            if ($this->can($scopes, $scope)) {
                return $request;
            }
        }

        throw AuthenticationException::missingScope($scopes);
    }

    protected function validateRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        $request = $this->server->validateAuthenticatedRequest($request);

        $this->clientId = $request->getAttribute('oauth_client_id');
        $this->userId = $request->getAttribute('oauth_user_id');
        $this->tokenScopes = $request->getAttribute('oauth_scopes') ?? [];

        return $request;
    }

    protected function can(array $tokenScopes, string $scope): bool
    {
        $scopes = [$scope];

        foreach ($scopes as $scope) {
            if (array_key_exists($scope, array_flip($tokenScopes))) {
                return true;
            }
        }

        return false;
    }

    protected function getOption(ServerRequestInterface $request, string $key, mixed $default): mixed
    {
        $dispatched = $request->getAttribute(Dispatched::class);

        return $dispatched->handler?->options[$key] ?? $default;
    }
}
