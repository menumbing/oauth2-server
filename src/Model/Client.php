<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Model;

use Hyperf\Database\Model\Concerns\HasUuids;
use Menumbing\OAuth2\Server\Contract\ClientModelInterface;
use Menumbing\Orm\Contract\CacheableInterface;
use Menumbing\Orm\Model;
use Menumbing\Orm\Relation\BelongsTo;
use Menumbing\Orm\Trait\Cacheable;

use function Hyperf\Config\config;

/**
 * @property User $user
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Client extends Model implements ClientModelInterface, CacheableInterface
{
    use HasUuids,Cacheable;

    protected null|string $table = 'oauth_clients';

    protected array $guarded = [];

    protected array $hidden = [
        'secret',
    ];

    protected array $casts = [
        'personal_access_client' => 'bool',
        'password_client' => 'bool',
        'revoked' => 'bool',
        'allowed_scopes' => 'array',
        'forbid_scopes' => 'array',
    ];

    protected ?string $plainSecret = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIdentifier(): string
    {
        return $this->getKey();
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function getRedirect(): string
    {
        return $this->getAttribute('redirect') ?? '';
    }

    public function setSecretAttribute($value): void
    {
        $this->plainSecret = $value;

        if (!config('oauth2_server.hashes_client_secret')) {
            $this->attributes['secret'] = $value;

            return;
        }

        $this->attributes['secret'] = password_hash($value, PASSWORD_BCRYPT);
    }

    public function getSecret(): ?string
    {
        return $this->getAttribute('secret');
    }

    public function getPlainSecret(): ?string
    {
        return $this->plainSecret;
    }

    public function isFirstParty(): bool
    {
        return empty($this->getAttribute('user_id'));
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

    public function handlesGrant(?string $grantType): bool
    {
        return match ($grantType) {
            'authorization_code', 'implicit' => !empty($this->getRedirect()),
            'personal_access'                => $this->isPersonalAccessClient() && $this->isConfidential(),
            'password'                       => $this->isPasswordClient(),
            'client_credentials'             => $this->isConfidential() && $this->isFirstParty(),
            default                          => false,
        };
    }

    public function hasScopeControls(): bool
    {
        return !empty($this->getAttribute('allowed_scopes')) || !empty($this->getAttribute('forbid_scopes'));
    }

    public function isAllow(string $scope): bool
    {
        if (empty($allowedScopes = $this->getAttribute('allowed_scopes') ?? [])) {
            return true;
        }

        return in_array($scope, $allowedScopes);
    }

    public function isForbid(string $scope): bool
    {
        if (empty($forbidScopes = $this->getAttribute('forbid_scopes') ?? [])) {
            return false;
        }

        return in_array($scope, $forbidScopes);
    }
}
