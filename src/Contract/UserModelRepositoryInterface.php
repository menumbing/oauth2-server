<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Contract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface UserModelRepositoryInterface
{
    public function findActive(string $username): ?UserModelInterface;
}
