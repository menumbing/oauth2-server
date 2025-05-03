<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Contract;

use HyperfExtension\Auth\Contracts\GuardInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface OAuth2GuardInterface extends GuardInterface
{
    public function client(): ?ClientModelInterface;

    public function tokenScopes(): array;
}
