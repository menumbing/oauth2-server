<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Http\Controller;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Menumbing\OAuth2\Server\Contract\TokenIssuerInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class IssueTokenController
{
    public function __construct(protected TokenIssuerInterface $tokenIssuer)
    {
    }

    public function issueToken(RequestInterface $request, ResponseInterface $response)
    {
        return $this->tokenIssuer->issueToken($request, $response);
    }
}
