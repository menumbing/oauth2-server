<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Menumbing\OAuth2\Server\Contract\AuthCodeModelRepositoryInterface;
use Menumbing\OAuth2\Server\Contract\TokenModelInterface;
use Menumbing\OAuth2\Server\Model\AuthCode;
use Menumbing\Orm\Annotation\AsRepository;
use Menumbing\Orm\Repository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[AsRepository(modelClass: AuthCode::class)]
class AuthCodeModelRepository extends Repository implements AuthCodeModelRepositoryInterface
{
    public function findByCodeId(string $codeId): ?TokenModelInterface
    {
        return $this->findById($codeId);
    }

    public function create(array $attributes): TokenModelInterface
    {
        return $this->save(
            $this->model->newInstance()->fill($attributes)
        );
    }

    public function revoke(string $codeId): void
    {
        $this->save(
            $this->findById($codeId)->revoke()
        );
    }
}
