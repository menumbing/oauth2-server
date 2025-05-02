<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Contract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface TokenModelRepositoryInterface
{
    public function findByCodeId(string $codeId): ?TokenModelInterface;

    public function create(array $attributes): TokenModelInterface;

    public function revoke(string $tokenId): void;

    public function purgeAllRevokedAndExpired(): void;
}
