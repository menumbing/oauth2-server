<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
readonly class RefreshTokenCreated
{
    public function __construct(public string $refreshTokenId, public string $accessTokenId)
    {
    }
}
