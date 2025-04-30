<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Model;

use Menumbing\Orm\Model;

use function Hyperf\Config\config;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class Token extends Model
{
    public bool $incrementing = false;

    public array $guarded = [];

    protected array $casts = [
        'revoked' => 'bool',
    ];

    protected array $dates = [
        'expires_at',
    ];

    protected string $keyType = 'string';

    public function revoke(): static
    {
        $this->setAttribute('revoked', true);

        return $this;
    }

    public function isRevoked(): bool
    {
        return true === $this->getAttribute('revoked');
    }

    public function getConnectionName(): string
    {
        return config('oauth2.database.connection', $this->connection);
    }
}
