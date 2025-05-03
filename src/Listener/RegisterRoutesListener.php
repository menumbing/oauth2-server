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
        $issueTokenRoute = $this->config->get('oauth2-server.routes.issue_token', []);

        $this->getRouter()->addRoute(
            httpMethod: 'POST',
            route: $issueTokenRoute['path'] ?? '/oauth/token',
            handler: $issueTokenRoute['handler'] ?? [IssueTokenController::class, 'issueToken'],
            options: $isueTokenRoute['options'] ?? [],
        );

        $this->addRouteIfExist('GET', 'user_info');
        $this->addRouteIfExist('GET', 'scope_list');
        $this->addRouteIfExist('GET', 'token_validity');
    }

    private function addRouteIfExist(string $httpMethod, string $routeKey): void
    {
        if (!empty($route = $this->config->get('oauth2-server.routes.' . $routeKey))) {
            $this->getRouter()->addRoute($httpMethod, $route['path'], $route['handler'], $route['options'] ?? []);
        }
    }

    private function getRouter(): RouteCollector
    {
        return $this->dispatcherFactory->getRouter(
            $this->config->get('oauth2-server.route.server', 'http')
        );
    }
}
