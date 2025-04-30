<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge\Entity;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RefreshToken implements RefreshTokenEntityInterface
{
    use EntityTrait, RefreshTokenTrait;
}
