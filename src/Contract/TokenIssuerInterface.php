<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Contract;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface TokenIssuerInterface
{
    public function byUser(mixed $user, ClientModelInterface $client, array $scopes = []): ResponseInterface;

    public function refreshToken(string $refreshToken, ClientModelInterface $client, array $scopes = []): ResponseInterface;

    public function issueToken(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
