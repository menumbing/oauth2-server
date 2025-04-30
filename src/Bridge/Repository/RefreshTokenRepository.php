<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge\Repository;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Menumbing\OAuth2\Server\Bridge\Entity\RefreshToken;
use Menumbing\OAuth2\Server\Contract\RefreshTokenModelRepositoryInterface;
use Menumbing\OAuth2\Server\Event\RefreshTokenCreated;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(
        protected RefreshTokenModelRepositoryInterface $modelRepository,
        protected EventDispatcherInterface $events,
    ) {
    }

    public function getNewRefreshToken(): ?RefreshTokenEntityInterface
    {
        return new RefreshToken();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $this->modelRepository->create([
            'id' => $id = $refreshTokenEntity->getIdentifier(),
            'access_token_id' => $accessTokenId = $refreshTokenEntity->getAccessToken()->getIdentifier(),
            'revoked' => false,
            'expires_at' => $refreshTokenEntity->getExpiryDateTime(),
        ]);

        $this->events->dispatch(new RefreshTokenCreated($id, $accessTokenId));
    }

    public function revokeRefreshToken(string $tokenId): void
    {
        $this->modelRepository->revoke($tokenId);
    }

    public function isRefreshTokenRevoked(string $tokenId): bool
    {
        return true === $this->modelRepository->findByCodeId($tokenId)?->isRevoked();
    }
}
