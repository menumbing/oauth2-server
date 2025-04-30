<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge\Repository;

use Hyperf\Contract\ConfigInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Menumbing\OAuth2\Server\Bridge\Entity\Client;
use Menumbing\OAuth2\Server\Contract\ClientModelRepositoryInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ClientRepository implements ClientRepositoryInterface
{
    public function __construct(protected ClientModelRepositoryInterface $modelRepository, protected ConfigInterface $config)
    {
    }

    public function getClientEntity(string $clientIdentifier): ?ClientEntityInterface
    {
        if (null === $record = $this->modelRepository->findActive($clientIdentifier)) {
            return null;
        }

        return new Client(
            identifier: $clientIdentifier,
            name: $record->getName(),
            redirectUri: $record->getRedirect(),
            isConfidential: $record->isConfidential(),
        );
    }

    public function validateClient(string $clientIdentifier, ?string $clientSecret, ?string $grantType): bool
    {
        $record = $this->modelRepository->findActive($clientIdentifier);

        if (!$record || !$record->handlesGrant($grantType)) {
            return false;
        }

        return !$record->isConfidential() || $this->verifySecret($clientSecret, $record->getSecret());
    }

    protected function verifySecret(string $clientSecret, string $storedHash): bool
    {
        return $this->config->get('oauth2.hashes_client_secret')
            ? password_verify($clientSecret, $storedHash)
            : hash_equals($clientSecret, $storedHash);
    }
}
