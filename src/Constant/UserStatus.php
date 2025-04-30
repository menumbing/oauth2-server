<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Constant;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
