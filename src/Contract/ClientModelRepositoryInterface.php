<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Contract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ClientModelRepositoryInterface
{
    public function findActive(string $identifier): ?ClientModelInterface;

    public function create(?string $userId, string $name, string $redirect, bool $personalAccess = false, bool $password = false, bool $confidential = true): ClientModelInterface;

    public function createPasswordGrantClient(?string $userId, string $name, string $redirect): ClientModelInterface;
}
