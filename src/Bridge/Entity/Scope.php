<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\ScopeTrait;

use function Hyperf\Tappable\tap;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Scope implements ScopeEntityInterface
{
    use EntityTrait;
    use ScopeTrait;

    public static function create(string $identifier): static
    {
        return tap(new self(), function (Scope $scope) use ($identifier) {
            $scope->identifier = $identifier;
        });
    }
}
