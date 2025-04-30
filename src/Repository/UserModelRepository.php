<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Repository;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Menumbing\OAuth2\Server\Constant\UserStatus;
use Menumbing\OAuth2\Server\Contract\UserModelInterface;
use Menumbing\OAuth2\Server\Contract\UserModelRepositoryInterface;
use Menumbing\OAuth2\Server\Model\User;
use Menumbing\Orm\Annotation\AsRepository;
use Menumbing\Orm\Repository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[AsRepository(modelClass: User::class)]
class UserModelRepository extends Repository implements UserModelRepositoryInterface
{
    #[Inject]
    protected ConfigInterface $config;

    public function findActive(string $username): ?UserModelInterface
    {
        $qb = $this->query;

        $qb
            ->where($this->config->get('oauth2.user_find_by', 'email'), $username)
            ->where('status', UserStatus::ACTIVE->value);
        ;

        return $qb->first();
    }
}
