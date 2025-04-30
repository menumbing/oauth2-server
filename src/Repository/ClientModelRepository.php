<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Menumbing\OAuth2\Server\Contract\ClientModelInterface;
use Menumbing\OAuth2\Server\Contract\ClientModelRepositoryInterface;
use Menumbing\OAuth2\Server\Model\Client;
use Menumbing\Orm\Annotation\AsRepository;
use Menumbing\Orm\Repository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[AsRepository(modelClass: Client::class)]
class ClientModelRepository extends Repository implements ClientModelRepositoryInterface
{
    public function findActive(string $identifier): ?ClientModelInterface
    {
        return $this->findOneBy([$this->model->getKeyName() => $identifier, 'revoked' => false]);
    }
}
