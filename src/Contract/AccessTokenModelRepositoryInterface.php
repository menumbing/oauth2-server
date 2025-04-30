<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Contract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface AccessTokenModelRepositoryInterface
{
    public function findByCodeId(string $codeId): ?TokenModelInterface;

    public function create(array $attributes): void;

    public function revoke(string $tokenId): void;
}
