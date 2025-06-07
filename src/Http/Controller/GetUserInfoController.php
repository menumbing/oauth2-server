<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Http\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Router\Dispatched;
use HyperfExtension\Auth\Annotations\AuthUser;
use HyperfExtension\Auth\Contracts\AuthManagerInterface;
use Menumbing\OAuth2\Server\Contract\OAuth2GuardInterface;
use Menumbing\OAuth2\Server\Exception\AuthenticationException;
use Menumbing\Orm\Model;
use Psr\Http\Message\ServerRequestInterface;

use function Hyperf\Collection\collect;
use function Hyperf\Config\config;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class GetUserInfoController
{
    #[Inject]
    protected ServerRequestInterface $request;

    #[Inject]
    protected AuthManagerInterface $auth;

    #[AuthUser(for: 'user')]
    public function infoMe(?Model $user, ServerRequestInterface $request): array
    {
        if (null === $user) {
            throw AuthenticationException::accessDenied();
        }

        $data = ['id' => $user->id, 'status' => $user->status];
        $items = collect($user->toArray());
        $tokenScopes = $this->guard($request)->tokenScopes();

        foreach (config('oauth2-server.user_info_fields', []) as $scope => $fields) {
            if (!in_array($scope, $tokenScopes)) {
                continue;
            }

            $fields = explode(',', str_replace(' ', '', $fields));
            $data = array_merge($data, $items->only($fields)->toArray());
        }

        return $data;
    }

    /**
     * @param  ServerRequestInterface  $request
     *
     * @return OAuth2GuardInterface|mixed
     */
    protected function guard(ServerRequestInterface $request): OAuth2GuardInterface
    {
        $dispatched = $request->getAttribute(Dispatched::class);

        return $this->auth->guard($dispatched->handler?->options['guard'] ?? null);
    }
}
