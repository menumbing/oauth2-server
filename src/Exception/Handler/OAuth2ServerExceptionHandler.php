<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use League\OAuth2\Server\Exception\OAuthServerException;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

/**
 * @author  Aldi Arief <aldiarief598@gmail.com>
 */
class OAuth2ServerExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable|OAuthServerException $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();

        return $throwable->generateHttpResponse($response);
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof OAuthServerException;
    }
}