<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Contract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ClientModelRepositoryInterface
{
    public function findActive(string $identifier): ?ClientModelInterface;
}
