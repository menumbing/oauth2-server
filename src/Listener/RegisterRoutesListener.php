<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\HttpServer\Router\RouteCollector;
use Menumbing\OAuth2\Server\Http\Controller\IssueTokenController;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class RegisterRoutesListener implements ListenerInterface
{
    public function __construct(
        private DispatcherFactory $dispatcherFactory,
        private ConfigInterface $config,
    ) {
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        $path = $this->config->get('oauth2-server.route.path', '/oauth/token');
        $middleware = $this->config->get('oauth2-server.route.middleware', []);
        $handler = $this->config->get('oauth2-server.route.handler', [IssueTokenController::class, 'issueToken']);

        $this->getRouter()->addRoute(['POST'], $path, $handler, ['middleware' => $middleware]);
    }

    private function getRouter(): RouteCollector
    {
        return $this->dispatcherFactory->getRouter(
            $this->config->get('oauth2-server.route.server', 'http')
        );
    }
}
