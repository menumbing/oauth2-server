<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Hyperf\Stringable\Str;
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

    public function create(?string $userId, string $name, string $redirect, bool $personalAccess = false, bool $password = false, bool $confidential = true): ClientModelInterface
    {
        $client = $this->model->newInstance()->fill([
            'user_id' => $userId,
            'name' => $name,
            'secret' => ($confidential || $personalAccess) ? Str::random(40) : null,
            'redirect' => $redirect,
            'personal_access_client' => $personalAccess,
            'password_client' => $password,
            'revoked' => false,
        ]);

        return $this->save($client);
    }

    public function createPasswordGrantClient(?string $userId, string $name, string $redirect): ClientModelInterface
    {
        return $this->create($userId, $name, $redirect, false, true);
    }
}
