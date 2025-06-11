<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Http\Controller;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Menumbing\OAuth2\Server\Contract\TokenModelRepositoryInterface;
use Menumbing\OAuth2\Server\Repository\AccessTokenModelRepository;
use Psr\Container\ContainerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class GetAccessTokenInfoController
{
    #[Inject]
    protected ConfigInterface $config;

    #[Inject]
    protected ContainerInterface $container;

    protected TokenModelRepositoryInterface $tokenRepository;

    public function __construct()
    {
        $this->tokenRepository = $this->container->get(
            $this->config->get('oauth2_server.repositories.access_token', AccessTokenModelRepository::class)
        );
    }

    public function isRevoked(string $tokenId): array
    {
        if (null !== $token = $this->tokenRepository->findByCodeId($tokenId)) {
            return ['revoked' => $token->isRevoked()];
        }

        throw new \LogicException('Token not found.', 404);
    }
}
