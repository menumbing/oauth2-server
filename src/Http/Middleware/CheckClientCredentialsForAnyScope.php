<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Http\Middleware;

use Hyperf\Contract\ConfigInterface;
use League\OAuth2\Server\ResourceServer;
use Menumbing\OAuth2\Server\Contract\ClientModelRepositoryInterface;
use Menumbing\OAuth2\Server\Exception\AuthenticationException;
use Menumbing\OAuth2\Server\Repository\ClientModelRepository;
use Menumbing\OAuth2\Server\ValidateScopes;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class CheckClientCredentialsForAnyScope implements MiddlewareInterface
{
    use ValidateScopes;

    protected ClientModelRepositoryInterface $clientRepository;

    public function __construct(
        ContainerInterface $container,
        ConfigInterface $config,
        ResourceServer $server
    ) {
        $this->clientRepository = $container->get(
            $config->get('oauth2_server.repositories.client', ClientModelRepository::class)
        );

        $this->server = $server;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->validateScopes($request);

        if (null === $client = $this->clientRepository->findActive($this->clientId)) {
            throw new AuthenticationException();
        }

        if ($client->isPasswordClient()) {
            throw new AuthenticationException();
        }

        $request->withAttribute('oauth_client_id', $this->clientId);
        $request->withAttribute('token_scopes', $this->tokenScopes);

        return $handler->handle($request);
    }
}
