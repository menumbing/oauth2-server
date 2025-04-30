<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge;

use Menumbing\OAuth2\Server\Bridge\Entity\Scope;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait FormatScopesForStorage
{
    /**
     * Format the given scopes for storage.
     */
    public function formatScopesForStorage(array $scopes): string
    {
        return json_encode($this->scopesToArray($scopes));
    }

    /**
     * Get an array of scope identifiers for storage.
     */
    public function scopesToArray(array $scopes): array
    {
        return array_map(fn(Scope $scope) => $scope->getIdentifier(), $scopes);
    }
}
