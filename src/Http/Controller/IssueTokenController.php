<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Http\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Menumbing\OAuth2\Server\Contract\TokenIssuerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class IssueTokenController
{
    #[Inject]
    protected TokenIssuerInterface $tokenIssuer;

    public function __construct()
    {
    }

    public function issueToken(RequestInterface $request, ResponseInterface $response)
    {
        return $this->tokenIssuer->issueToken($request, $response);
    }
}
