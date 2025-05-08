<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Guard;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Stringable\Str;
use HyperfExtension\Auth\Contracts\AuthenticatableInterface;
use HyperfExtension\Auth\Contracts\UserProviderInterface;
use HyperfExtension\Auth\GuardHelpers;
use League\OAuth2\Server\ResourceServer;
use Menumbing\OAuth2\Server\Contract\ClientModelInterface;
use Menumbing\OAuth2\Server\Contract\ClientModelRepositoryInterface;
use Menumbing\OAuth2\Server\Contract\OAuth2GuardInterface;
use Menumbing\OAuth2\Server\Repository\AccessTokenModelRepository;
use Menumbing\OAuth2\Server\Repository\ClientModelRepository;
use Menumbing\OAuth2\Server\ValidateScopes;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class OAuth2Guard implements OAuth2GuardInterface
{
    use GuardHelpers, ValidateScopes;

    protected ClientModelRepositoryInterface $clientRepository;

    protected AccessTokenModelRepository $accessTokenRepository;

    protected ?ClientModelInterface $client = null;

    protected array $tokenScopes = [];

    public function __construct(
        protected RequestInterface $request,
        UserProviderInterface $provider,
        ResourceServer $server,
        protected ContainerInterface $container,
        protected ConfigInterface $config,
        protected string $name,
        protected array $options = [],
    ) {
        $this->provider = $provider;
        $this->clientRepository = $this->getRepository('client', ClientModelRepository::class);
        $this->accessTokenRepository = $this->getRepository('access_token', AccessTokenModelRepository::class);
        $this->server = $server;
    }

    public function user(): ?AuthenticatableInterface
    {
        if (null !== $this->user) {
            return $this->user;
        }

        $user = null;

        if ($this->bearerToken($this->request)) {
            $user = $this->authenticateWithBearerToken($this->request);
        } elseif ($token = $this->request->cookie($this->config->get('oauth2-server.cookie.name', 'oauth2_token'))) {
            $request = $this->request->withHeader('Authorization', 'Bearer ' . $token);
            $user = $this->authenticateWithBearerToken($request);;
        }

        return $this->user = $user;
    }

    public function validate(array $credentials = []): bool
    {
        return $this->hasValidCredentials(
            $this->provider->retrieveByCredentials($credentials),
            $credentials
        );
    }

    protected function hasValidCredentials(?AuthenticatableInterface $user, array $credentials): bool
    {
        return null !== $user && $this->provider->validateCredentials($user, $credentials);
    }

    protected function authenticateWithBearerToken(ServerRequestInterface $request): ?AuthenticatableInterface
    {
        if (null === $request = $this->getPsrRequestViaBearerToken($request)) {
            return null;
        }

        if (null === $user = $this->provider->retrieveById($request->getAttribute('oauth_user_id') ?: null)) {
            return null;
        }

        if (null === $this->clientRepository->findActive($request->getAttribute('oauth_client_id'))) {
            return null;
        }

        $token = $this->accessTokenRepository->findByCodeId($request->getAttribute('oauth_access_token_id'));

        return $token ? $user->withAccessToken($token) : null;
    }

    protected function getPsrRequestViaBearerToken(ServerRequestInterface $request): ?ServerRequestInterface
    {
        $request = $this->validateScopes($request);

        if (null === $client = $this->clientRepository->findActive($request->getAttribute('oauth_client_id'))) {
            return null;
        }

        $this->client = $client;
        $this->tokenScopes = $request->getAttribute('oauth_scopes') ?? [];

        return $request;
    }

    /**
     * @template T
     *
     * @param  string  $type
     * @param  class-string<T>  $default
     *
     * @return T
     */
    protected function getRepository(string $type, string $default): mixed
    {
        return $this->container->get(
            $this->config->get('oauth2-server.repositories.' . $type, $default),
        );
    }

    public function client(): ?ClientModelInterface
    {
        return $this->client;
    }

    public function tokenScopes(): array
    {
        return $this->tokenScopes;
    }

    protected function bearerToken(RequestInterface $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');
        if (Str::startsWith($header, 'Bearer ')) {
            return Str::substr($header, 7);
        }

        return null;
    }
}
