<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Model;

use Hyperf\Database\Model\Concerns\HasUuids;
use HyperfExtension\Auth\Authenticatable;
use HyperfExtension\Auth\Contracts\AuthenticatableInterface;
use Menumbing\Hashing\Cast\Hash;
use Menumbing\OAuth2\Server\Constant\UserStatus;
use Menumbing\OAuth2\Server\Contract\UserModelInterface;
use Menumbing\OAuth2\Server\HasApiTokens;
use Menumbing\Orm\Contract\CacheableInterface;
use Menumbing\Orm\Model;
use Menumbing\Orm\Trait\Cacheable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class User extends Model implements UserModelInterface, AuthenticatableInterface, CacheableInterface
{
    use HasUuids, Authenticatable, HasApiTokens, Cacheable;

    protected array $guarded = [];

    protected array $hidden = [
        'password',
    ];

    protected array $casts = [
        'status' => UserStatus::class,
        'password' => Hash::class,
    ];

    public function getIdentifier(): string
    {
        return $this->getKey();
    }

    public function getPassword(): string
    {
        return $this->getAttribute('password');
    }
}
