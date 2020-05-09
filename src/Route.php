<?php
declare(strict_types=1);

namespace Air\Routing;

use Air\Routing\Route\RouteLeaf;
use Air\Routing\Route\RouteTree;
use Air\Routing\Exception\RouteException;

/**
 * Class Route
 * @package Air\Route
 *
 * @method Route any(string $uri, string $handle)
 * @method Route cli(string $uri, string $handle)
 * @method Route get(string $uri, string $handle)
 * @method Route put(string $uri, string $handle)
 * @method Route post(string $uri, string $handle)
 * @method Route head(string $uri, string $handle)
 * @method Route patch(string $uri, string $handle)
 * @method Route delete(string $uri, string $handle)
 */
class Route implements RouteInterface
{
    /**
     * @var RouteTree
     */
    private $routeTree;

    /**
     * @var RouteLeaf
     */
    private $routeLeaf;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var int
     */
    private $groupLevel = 0;

    /**
     * Router constructor
     */
    public function __construct()
    {
        $this->routeTree = new RouteTree();
        $this->routeLeaf = new RouteLeaf();
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function prefix(string $prefix)
    {
        $this->attributes['prefix'][$this->groupLevel] = rtrim($prefix, '/');

        return $this;
    }

    /**
     * @param $config
     * @return $this
     */
    public function config(array $config)
    {
        $this->attributes['config'][$this->groupLevel] = $config;

        return $this;
    }

    /**
     * @param callable $callback
     */
    public function group(callable $callback)
    {
        $this->groupLevel++;
        $callback($this);

        $groupLevel = $this->groupLevel--;
        unset(
            $this->attributes['prefix'][$groupLevel],
            $this->attributes['config'][$groupLevel]
        );

        $groupLevel = $this->groupLevel--;
        unset(
            $this->attributes['prefix'][$groupLevel],
            $this->attributes['config'][$groupLevel]
        );
    }

    /**
     * @param $method
     * @param $uri
     * @param $handle
     */
    public function addRoute($method, string $uri, string $handle)
    {
        $this->setUri($uri)
            ->setMethod($method)
            ->setConfig()
            ->setHandler($handle);

        $this->getRouteTree()
            ->insert($this->getRouteLeaf());
    }

    /**
     * @param string $uri
     * @return RouteLeaf|null
     */
    public function dispatch(string $uri)
    {
        return $this->getRouteTree()->search($uri, $this->getRouteLeaf());
    }

    /**
     * @param $method
     * @param $arguments
     * @return $this
     * @throws RouteException
     */
    public function __call($method, $arguments)
    {
        if (in_array($method, ['get', 'any', 'cli', 'put', 'head', 'post', 'patch', 'delete', 'options'])) {
            $this->addRoute(strtoupper($method), ...$arguments);

            return $this;
        }

        throw new RouteException('Call to undefined method ' . __CLASS__ . '::' . $method . '()');
    }

    /**
     * @return RouteLeaf
     */
    public function getRouteLeaf() : RouteLeaf
    {
        return $this->routeLeaf;
    }

    /**
     * @return RouteTree
     */
    public function getRouteTree() : RouteTree
    {
        return $this->routeTree;
    }

    /**
     * @param string $uri
     * @return $this
     */
    private function setUri(string $uri)
    {
        $prefix = '';
        if (isset($this->attributes['prefix'])) {
            $prefix = join('/', $this->attributes['prefix']);
        }
        $this->getRouteLeaf()->setUri($prefix . $uri);

        return $this;
    }

    /**
     * @param $method
     * @return $this
     */
    private function setMethod($method)
    {
        $this->getRouteLeaf()->setMethod((array)$method);

        return $this;
    }

    /**
     * @return $this
     */
    private function setConfig()
    {
        $this->getRouteLeaf()->setConfig(array_merge_recursive(...($this->attributes['config'] ?? [])));

        return $this;
    }

    /**
     * @param string $handle
     * @return $this
     */
    private function setHandler(string $handle)
    {
        $this->getRouteLeaf()->setHandler($handle);

        return $this;
    }
}
