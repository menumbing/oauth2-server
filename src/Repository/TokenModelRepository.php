<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Carbon\Carbon;
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

    public function create(array $attributes): TokenModelInterface
    {
        $this->save(
            $token = $this->model->newInstance()->fill($attributes)
        );

        return $token;
    }

    public function revoke(string $tokenId): void
    {
        $this->save(
            $this->findById($tokenId)->revoke()
        );
    }

    public function purgeAllRevokedAndExpired(): void
    {
        $now = Carbon::now()->subDays(7)->format('Y-m-d H:i:s');

        $this->query
            ->where('revoked', true)
            ->orWhere('expires_at', '<=', $now)
            ->delete();
    }
}
