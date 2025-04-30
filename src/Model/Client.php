<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Model;

use Hyperf\Database\Model\Concerns\HasUuids;
use Menumbing\OAuth2\Server\Contract\ClientModelInterface;
use Menumbing\Orm\Model;
use Menumbing\Orm\Relation\BelongsTo;

/**
 * @property User $user
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Client extends Model implements ClientModelInterface
{
    use HasUuids;

    protected null|string $table = 'oauth_clients';

    protected array $fillable = [
        'user_id',
        'name',
        'secret',
        'redirect',
        'personal_access_client',
        'password_client',
        'revoked',
        'allow_scopes',
        'allow_grant_types',
    ];

    protected array $hidden = [
        'secret',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getRedirect(): string
    {
        return $this->getAttribute('redirect') ?? '';
    }

    public function getSecret(): string
    {
        return $this->getAttribute('secret');
    }

    public function isFirstParty(): bool
    {
        return $this->getAttribute('personal_access_client') || $this->getAttribute('password_client');
    }

    public function shouldSkipAuthorization(): bool
    {
        return $this->isFirstParty();
    }

    public function isPersonalAccessClient(): bool
    {
        return $this->getAttribute('personal_access_client');
    }

    public function isPasswordClient(): bool
    {
        return $this->getAttribute('password_client');
    }

    public function isConfidential(): bool
    {
        return !empty($this->getSecret());
    }
}
