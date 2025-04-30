<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Menumbing\OAuth2\Server\Contract\TokenModelInterface;
use Menumbing\Orm\Repository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class TokenModelRepository extends Repository
{
    public function findByCodeId(string $codeId): ?TokenModelInterface
    {
        return $this->findById($codeId);
    }

    public function create(array $attributes): void
    {
        $this->save(
            $this->model->newInstance()->fill($attributes)
        );
    }

    public function revoke(string $tokenId): void
    {
        $this->save(
            $this->findById($tokenId)->revoke()
        );
    }
}
