<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Menumbing\OAuth2\Server\Contract\TokenModelRepositoryInterface;
use Menumbing\OAuth2\Server\Model\AccessToken;
use Menumbing\Orm\Annotation\AsRepository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[AsRepository(modelClass: AccessToken::class)]
class AccessTokenModelRepository extends TokenModelRepository implements TokenModelRepositoryInterface
{
    protected ?string $cachePrefix = 'ACCESS_TOKEN_';
}
