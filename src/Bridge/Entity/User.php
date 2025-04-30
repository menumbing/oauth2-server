<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge\Entity;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class User implements UserEntityInterface
{
    use EntityTrait;

    public function __construct(string $identifier)
    {
        $this->setIdentifier($identifier);
    }
}
