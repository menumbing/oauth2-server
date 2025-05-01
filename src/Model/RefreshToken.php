<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Model;

use Menumbing\OAuth2\Server\Contract\TokenModelInterface;
use Menumbing\Orm\Relation\BelongsTo;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RefreshToken extends Token implements TokenModelInterface
{
    public null|string $table = 'oauth_refresh_tokens';

    public function accessToken(): BelongsTo
    {
        return $this->belongsTo(AccessToken::class);
    }
}
