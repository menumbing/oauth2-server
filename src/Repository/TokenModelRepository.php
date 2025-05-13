<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Carbon\Carbon;
use Menumbing\OAuth2\Server\Contract\TokenModelInterface;
use Menumbing\Orm\Model;
use Menumbing\Orm\Repository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class TokenModelRepository extends Repository
{
    use CacheableToken;

    public function findByCodeId(string $codeId): ?TokenModelInterface
    {
        if ($this->hasCache($codeId)) {
            return $this->getCache($codeId);
        }

        return $this->cache($this->findById($codeId));
    }

    public function create(array $attributes): TokenModelInterface
    {
        return $this->save($this->newModel($attributes));
    }

    public function revoke(string $tokenId): void
    {
        $this->save(
            $this->findById($tokenId)->revoke()
        );

        $this->removeCache($tokenId);
    }

    public function purgeAllRevokedAndExpired(): void
    {
        $now = Carbon::now()->subDays(7)->format('Y-m-d H:i:s');

        $this->query
            ->where('revoked', true)
            ->orWhere('expires_at', '<=', $now)
            ->delete();
    }

    protected function newModel(array $attributes): Model&TokenModelInterface
    {
        return $this->model->newInstance()->fill($attributes);
    }
}
