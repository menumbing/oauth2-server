<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Hyperf\Cache\CacheManager;
use Hyperf\Cache\Driver\DriverInterface;
use Hyperf\Context\ApplicationContext;
use Menumbing\OAuth2\Server\Contract\TokenModelInterface;
use Menumbing\Orm\Model;

use function Hyperf\Config\config;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait CacheableToken
{
    protected ?string $cachePrefix;

    protected ?CacheManager $cache;

    protected function hasCache(string $key): bool
    {
        if (! $this->isEnableCache()) {
            return false;
        }

        return $this->driver()->has($this->makeCacheKey($key));
    }

    protected function getCache(string $key): Model|TokenModelInterface|null
    {
        if ($this->hasCache($key)) {
            return $this->newModel(
                $this->driver()->get($this->makeCacheKey($key))
            );
        }

        return null;
    }

    protected function cache(Model $token): Model|TokenModelInterface
    {
        if ($this->isEnableCache()) {
            $this->driver()->set(
                $this->makeCacheKey($token->getKey()),
                $token->toArray(),
                config('oauth2_server.cache.ttl')
            );
        }

        return $token;
    }
    protected function removeCache(string $key): void
    {
        if ($this->hasCache($key)) {
            $this->driver()->delete($this->makeCacheKey($key));
        }
    }

    protected function isEnableCache(): bool
    {
        return true === config('oauth2_server.cache.enabled');
    }

    protected function driver(): DriverInterface
    {
        $cache = $this->cache ?? ApplicationContext::getContainer()->get(CacheManager::class);

        $this->cache = $cache;

        return $cache->getDriver(config('oauth2_server.cache.driver'));
    }

    protected function makeCacheKey(string $key): string
    {
        return ($this->cachePrefix ?? '') . $key;
    }

    abstract protected function newModel(array $attributes): Model&TokenModelInterface;
}
