<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge\Repository;

use Hyperf\Contract\ConfigInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Menumbing\OAuth2\Server\Bridge\Entity\Scope;

use function Hyperf\Collection\collect;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ScopeRepository implements ScopeRepositoryInterface
{
    protected array $scopes = [];

    public function __construct(ConfigInterface $config)
    {
        $this->scopes = $config->get('oauth2.scopes', []);
    }

    public function getScopeEntityByIdentifier(string $identifier): ?ScopeEntityInterface
    {
        if (!$this->hasScope($identifier)) {
            return null;
        }

        return Scope::create($identifier);
    }

    public function finalizeScopes(array $scopes, string $grantType, ClientEntityInterface $clientEntity, ?string $userIdentifier = null, ?string $authCodeId = null): array
    {
        if (! in_array($grantType, ['password', 'personal_access', 'client_credentials'])) {
            $scopes = collect($scopes)
                ->reject(fn(Scope $scope) => '*' === trim($scope->getIdentifier()))
                ->values()
                ->toArray();
        }

        return collect($scopes)
            ->filter(fn(Scope $scope) => $this->hasScope($scope->getIdentifier()))
            ->values()
            ->toArray();
    }

    public function hasScope(string $identifier): bool
    {
        return array_key_exists($identifier, $this->scopes);
    }
}
