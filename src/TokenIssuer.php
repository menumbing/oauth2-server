<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server;

use Hyperf\HttpServer\Contract\ResponseInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Menumbing\OAuth2\Server\Contract\ClientModelInterface;
use Menumbing\OAuth2\Server\Contract\TokenIssuerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class TokenIssuer implements TokenIssuerInterface
{
    public function __construct(
        private ServerRequestInterface $request,
        private ResponseInterface $response,
        private AuthorizationServer $authorizationServer,
    ) {
    }

    public function byUser(mixed $user, ClientModelInterface $client, array $scopes = []): PsrResponseInterface
    {
        $request = $this->request->withParsedBody([
            'grant_type'    => 'user',
            'client_id'     => $client->getIdentifier(),
            'client_secret' => $client->getSecret(),
            'scope'         => implode(' ', $scopes)
        ]);

        $request = $request->withAttribute('user', $user);

        return $this->issueToken($request, $this->response);
    }

    public function refreshToken(string $refreshToken, ClientModelInterface $client, array $scopes = []): PsrResponseInterface
    {
        $request = $this->request->withParsedBody([
            'grant_type'    => 'refresh_token',
            'client_id'     => $client->getIdentifier(),
            'client_secret' => $client->getSecret(),
            'refresh_token' => $refreshToken,
            'scope'         => implode(' ', $scopes)
        ]);

        return $this->issueToken($request, $this->response);
    }

    public function issueToken(ServerRequestInterface $request, PsrResponseInterface $response): PsrResponseInterface
    {
        try {
            return $this->authorizationServer->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $e) {
            $e->setPayload([
                ...$e->getPayload(),
                'code' => $e->getCode(),
            ]);

            return $e->generateHttpResponse($response);
        }
    }
}
