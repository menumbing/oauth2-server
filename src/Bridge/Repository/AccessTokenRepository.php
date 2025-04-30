<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge\Repository;

use DateTime;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Menumbing\OAuth2\Server\Bridge\Entity\AccessToken;
use Menumbing\OAuth2\Server\Bridge\FormatScopesForStorage;
use Menumbing\OAuth2\Server\Contract\AccessTokenModelRepositoryInterface;
use Menumbing\OAuth2\Server\Event\AccessTokenCreated;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    use FormatScopesForStorage;

    public function __construct(
        protected AccessTokenModelRepositoryInterface $modelRepository,
        protected EventDispatcherInterface $events,
    ) {
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, ?string $userIdentifier = null): AccessTokenEntityInterface
    {
        return new AccessToken($userIdentifier, $scopes, $clientEntity);
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $attributes = [
            'id'         => $accessTokenEntity->getIdentifier(),
            'user_id'    => $accessTokenEntity->getUserIdentifier(),
            'client_id'  => $accessTokenEntity->getClient()->getIdentifier(),
            'scopes'     => $this->scopesToArray($accessTokenEntity->getScopes()),
            'revoked'    => false,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
            'expires_at' => $accessTokenEntity->getExpiryDateTime(),
        ];

        $this->modelRepository->create($attributes);

        $this->events->dispatch(
            new AccessTokenCreated(
                $accessTokenEntity->getIdentifier(),
                $accessTokenEntity->getUserIdentifier(),
                $accessTokenEntity->getClient()->getIdentifier()
            )
        );
    }

    public function revokeAccessToken(string $tokenId): void
    {
        $this->modelRepository->revoke($tokenId);
    }

    public function isAccessTokenRevoked(string $tokenId): bool
    {
        return true === $this->modelRepository->findByCodeId($tokenId)?->isRevoked();
    }
}
