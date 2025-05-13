<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Menumbing\OAuth2\Server\Contract\RefreshTokenModelRepositoryInterface;
use Menumbing\OAuth2\Server\Model\RefreshToken;
use Menumbing\Orm\Annotation\AsRepository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[AsRepository(modelClass: RefreshToken::class)]
class RefreshTokenModelRepository extends TokenModelRepository implements RefreshTokenModelRepositoryInterface
{
    protected ?string $cachePrefix = 'REFRESH_TOKEN_';
}
