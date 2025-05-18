<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge\Repository;

use Hyperf\Contract\ConfigInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Menumbing\OAuth2\Server\Bridge\Entity\Scope;
use Menumbing\OAuth2\Server\Contract\ClientModelInterface;
use Menumbing\OAuth2\Server\Contract\ClientModelRepositoryInterface;
use Menumbing\OAuth2\Server\Exception\AuthenticationException;
use Menumbing\OAuth2\Server\Repository\ClientModelRepository;
use Psr\Container\ContainerInterface;

use function Hyperf\Collection\collect;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ScopeRepository implements ScopeRepositoryInterface
{
    protected array $scopes = [];

    protected ClientModelRepositoryInterface $clientModelRepository;

    public function __construct(ConfigInterface $config, ContainerInterface $container)
    {
        $this->scopes = $config->get('oauth2-server.scopes', []);
        $this->clientModelRepository = $container->get(
            $config->get('oauth2-server.repositories.client', ClientModelRepository::class),
        );
    }

    public function getAll(): array
    {
        return array_map(
            fn(string $key, string $label) => ['id' => $key, 'label' => $label],
            array_keys($this->scopes),
            array_values($this->scopes),
        );
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
        $client = $this->clientModelRepository->findActive($clientEntity->getIdentifier());

        if (! in_array($grantType, ['password', 'personal_access', 'client_credentials'])) {
            $scopes = collect($scopes)
                ->reject(fn(Scope $scope) => '*' === trim($scope->getIdentifier()))
                ->values()
                ->toArray();
        }

        return collect($scopes)
            ->filter(fn(Scope $scope) => $this->hasScope($scope->getIdentifier(), $client))
            ->values()
            ->toArray();
    }

    public function hasScope(string $identifier, ?ClientModelInterface $client = null): bool
    {
        if (null !== $client) {
            if ($client->hasScopeControls() && ($client->isForbid($identifier) || !$client->isAllow($identifier))) {
                throw AuthenticationException::forbiddenToUseScope($identifier);
            }
        }

        return array_key_exists($identifier, $this->scopes);
    }
}
