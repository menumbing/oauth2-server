<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge\Repository;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Menumbing\OAuth2\Server\Bridge\Entity\AuthCode;
use Menumbing\OAuth2\Server\Bridge\FormatScopesForStorage;
use Menumbing\OAuth2\Server\Contract\AuthCodeModelRepositoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    use FormatScopesForStorage;

    public function __construct(protected AuthCodeModelRepositoryInterface $modelRepository)
    {
    }

    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new AuthCode();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $attributes = [
            'id'         => $authCodeEntity->getIdentifier(),
            'user_id'    => $authCodeEntity->getUserIdentifier(),
            'client_id'  => $authCodeEntity->getClient()->getIdentifier(),
            'scopes'     => $this->formatScopesForStorage($authCodeEntity->getScopes()),
            'revoked'    => false,
            'expires_at' => $authCodeEntity->getExpiryDateTime(),
        ];

        $this->modelRepository->create($attributes);
    }

    public function revokeAuthCode(string $codeId): void
    {
        $this->modelRepository->revoke($codeId);
    }

    public function isAuthCodeRevoked(string $codeId): bool
    {
        return true === $this->modelRepository->findByCodeId($codeId)?->isRevoked();
    }
}
