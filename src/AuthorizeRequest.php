<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server;

use Carbon\Carbon;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpMessage\Cookie\Cookie;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequestInterface;
use Menumbing\OAuth2\Server\Bridge\Entity\User;
use Menumbing\OAuth2\Server\Contract\AuthorizeRequestInterface;
use Menumbing\OAuth2\Server\Contract\ClientModelRepositoryInterface;
use Menumbing\OAuth2\Server\Contract\UserModelInterface;
use Menumbing\OAuth2\Server\Repository\ClientModelRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class AuthorizeRequest implements AuthorizeRequestInterface
{
    private ClientModelRepositoryInterface $clientRepository;

    public function __construct(private AuthorizationServer $server, private ConfigInterface $config, ContainerInterface $container)
    {
        $this->clientRepository = $container->get(
            $config->get('oauth2-server.repositories.client', ClientModelRepository::class)
        );
    }

    public function authorize(UserModelInterface $user, ServerRequestInterface $request, ResponseInterface $response): AuthorizationRequestInterface
    {
        $authRequest = $this->server->validateAuthorizationRequest($request);

        $authRequest->setUser(new User($user->getIdentifier()));;

        return $authRequest;
    }

    public function finalize(AuthorizationRequestInterface $authRequest, ResponseInterface $response, bool $approved): ResponseInterface
    {
        $authRequest->setAuthorizationApproved($approved);
        $response = $this->server->completeAuthorizationRequest($authRequest, $response);

        if ($approved && $authRequest->getGrantTypeId() === 'implicit') {
            $client = $this->clientRepository->findActive($authRequest->getClient()->getIdentifier());

            if ($client?->isFirstParty()) {
                return $this->withCookie($response);
            }
        }

        return $response;
    }

    private function withCookie(ResponseInterface $response): ResponseInterface
    {
        $data = $this->parseResponse($response);
        $cookieName = $this->config->get('oauth2-server.cookie.name', 'oauth2_token');
        $domain = $this->config->get('oauth2-server.cookie.domain') ?? '';

        $cookie = new Cookie(
            $cookieName,
            $data['access_token'],
            Carbon::now()->addSeconds($data['expires_in']),
            '/',
            $domain,
            'https' === $data['scheme'],
            true,
            false,
            'Lax'
        );

        return $response->withAddedHeader('Set-Cookie', (string) $cookie);
    }

    private function parseResponse(ResponseInterface $response): array
    {
        $redirectUri = $response->getHeader('Location')[0];
        $parts = parse_url($redirectUri);
        parse_str($parts['fragment'], $data);

        return [
            'scheme' => $parts['scheme'],
            ...$data
        ];
    }
}
