<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Http\Controller;

use Hyperf\Di\Annotation\Inject;
use HyperfExtension\Auth\Annotations\Auth;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Menumbing\OAuth2\Server\Bridge\Repository\ScopeRepository;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[Auth]
class GetScopeListController
{
    #[Inject]
    protected ScopeRepositoryInterface $scopeRepository;

    public function index(): array
    {
        if ($this->scopeRepository instanceof ScopeRepository) {
            return $this->scopeRepository->getAll();
        }

        return [];
    }
}
