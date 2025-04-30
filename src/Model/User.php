<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Model;

use Hyperf\Database\Model\Concerns\HasUuids;
use Menumbing\Hashing\Cast\Hash;
use Menumbing\OAuth2\Server\Constant\UserStatus;
use Menumbing\OAuth2\Server\Contract\UserModelInterface;
use Menumbing\Orm\Model;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class User extends Model implements UserModelInterface
{
    use HasUuids;

    protected array $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

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
