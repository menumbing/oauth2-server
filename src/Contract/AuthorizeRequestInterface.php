<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Contract;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface AuthorizeRequestInterface
{
    public function authorize(UserModelInterface $user, ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
