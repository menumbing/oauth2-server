<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Exception;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ForbiddenScopeException extends \Exception
{
    public function __construct(string $scope, int $code = 403)
    {
        parent::__construct(sprintf('This client is forbidden to use scope "%s".', $scope), $code);
    }
}
