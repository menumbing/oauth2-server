<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Model;

use Menumbing\OAuth2\Server\Contract\TokenModelInterface;
use Menumbing\Orm\Relation\BelongsTo;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AuthCode extends Token implements TokenModelInterface
{
    public null|string $table = 'oauth_auth_codes';

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
