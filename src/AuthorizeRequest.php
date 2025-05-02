<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server;

use League\OAuth2\Server\AuthorizationServer;
use Menumbing\OAuth2\Server\Bridge\Entity\User;
use Menumbing\OAuth2\Server\Contract\AuthorizeRequestInterface;
use Menumbing\OAuth2\Server\Contract\UserModelInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class AuthorizeRequest implements AuthorizeRequestInterface
{
    public function __construct(private AuthorizationServer $server)
    {
    }

    public function authorize(UserModelInterface $user, ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $authRequest = $this->server->validateAuthorizationRequest($request);

        $authRequest->setUser(new User($user->getIdentifier()));;

        $authRequest->setAuthorizationApproved(true);

        return $this->server->completeAuthorizationRequest($authRequest, $response);
    }
}
