<?php
declare(strict_types=1);

namespace Air\Routing;

/**
 * Interface RouterInterface
 * @package Air\Router
 */
interface RouteInterface
{
    public function addRoute($method, string $route, $handler);
    public function dispatch(string $route, $method = null);
    public function getEngine(): EngineInterface;
}
