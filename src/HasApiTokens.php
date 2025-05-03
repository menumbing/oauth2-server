<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server;

use Menumbing\OAuth2\Server\Contract\TokenModelInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait HasApiTokens
{
    protected TokenModelInterface $token;

    public function token(): TokenModelInterface
    {
        return $this->token;
    }

    public function withAccessToken(TokenModelInterface $token): static
    {
        $this->token = $token;

        return $this;
    }
}
