<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
readonly class AccessTokenCreated
{
    public function __construct(
        public string $tokenId,
        public mixed $userId,
        public string $clientId,
    ) {
    }
}
