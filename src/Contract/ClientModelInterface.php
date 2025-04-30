<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Contract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ClientModelInterface
{
    public function getName(): string;

    public function getRedirect(): string;

    public function getSecret(): ?string;

    public function isFirstParty(): bool;

    public function shouldSkipAuthorization(): bool;

    public function isPersonalAccessClient(): bool;

    public function isPasswordClient(): bool;

    public function isConfidential(): bool;
}
