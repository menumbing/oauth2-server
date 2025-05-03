<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Exception;

use Hyperf\Collection\Arr;
use HyperfExtension\Auth\Exceptions\AuthorizationException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MissingScopeException extends AuthorizationException
{
    public readonly array $scopes;

    public function __construct($scopes = [], $message = 'Invalid scope(s) provided.', $code = 403)
    {
        parent::__construct($message, $code);

        $this->scopes = Arr::wrap($scopes);
    }
}
