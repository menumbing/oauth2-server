<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Menumbing\OAuth2\Server\Contract\AuthCodeModelRepositoryInterface;
use Menumbing\OAuth2\Server\Model\AuthCode;
use Menumbing\Orm\Annotation\AsRepository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[AsRepository(modelClass: AuthCode::class)]
class AuthCodeModelRepository extends TokenModelRepository implements AuthCodeModelRepositoryInterface
{
}
