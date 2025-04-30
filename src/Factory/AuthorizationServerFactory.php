<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Factory;

use Hyperf\Contract\ConfigInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Menumbing\Signature\Contract\ClientRepositoryInterface;
use Psr\Container\ContainerInterface;

use function Hyperf\Support\make;
use function Hyperf\Tappable\tap;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthorizationServerFactory
{
    protected ConfigInterface $config;

    public function __construct(protected ContainerInterface $container)
    {
        $this->config = $container->get(ConfigInterface::class);
    }

    public function __invoke()
    {
        return tap($this->makeAuthorizationServer(), function (AuthorizationServer $server) {
            foreach ($this->config->get('oauth2-server.grant_types', []) as $grant) {
                $expiresIn = in_array(AuthCodeGrant::class, class_implements($grant))
                    ? $this->config->get('oauth2-server.auth_code_expire_in')
                    : $this->config->get('oauth2-server.access_token_expire_in');

                $server->enableGrantType(
                    $this->makeGrant($grant),
                    $expiresIn
                );
            }
        });
    }

    protected function makeAuthorizationServer(): AuthorizationServer
    {
        return new AuthorizationServer(
            clientRepository: $this->container->get(ClientRepositoryInterface::class),
            accessTokenRepository: $this->container->get(AccessTokenRepositoryInterface::class),
            scopeRepository: $this->container->get(ScopeRepositoryInterface::class),
            privateKey: $this->config->get('oauth2-server.private_key'),
            encryptionKey: $this->config->get('oauth2-server.encryption_key'),
        );
    }

    protected function makeGrant(string $grantClass): GrantTypeInterface
    {
        return tap(make($grantClass), function (GrantTypeInterface $grant) {
            if ($grant instanceof ImplicitGrant) {
                return;
            }

            $grant->setRefreshTokenTTL($this->config->get('oauth2-server.refresh_token_expire_in'));
        });
    }
}
