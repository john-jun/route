<?php
declare(strict_types=1);

namespace Air\Routing;

use Air\Routing\Route\RouteLeaf;
use Air\Routing\Route\RouteTree;
use Air\Routing\Exception\RouteException;
use Closure;

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
     * @var array
     */
    private $prefix;

    /**
     * @var array
     */
    private $custom;

    /**
     * @var RouteTree
     */
    private $routeTree;

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
    }

    /**
     * set prefix
     * @param string $prefix
     * @return static
     */
    public function prefix(string $prefix)
    {
        $this->prefix[$this->groupLevel] = trim($prefix, '/');

        return $this;
    }

    /**
     * set addition args
     * @param $addition
     * @return $this
     */
    public function custom(array $addition)
    {
        $this->custom[$this->groupLevel] = $addition;

        return $this;
    }

    /**
     * add group route
     */
    public function group()
    {
        $args = func_get_args();

        $closure = array_pop($args);
        if (!$closure instanceof Closure) {
            return;
        }

        $attr = array_shift($args);
        if (is_string($attr)) {
            $this->prefix($this->mergePrefix($attr, $this->groupLevel));
        } elseif (is_array($attr)) {
            if (isset($attr['prefix'])) {
                $this->prefix($this->mergePrefix($attr['prefix'], $this->groupLevel));
            }

            if (isset($attr['custom'])) {
                $this->custom($this->mergeCustom((array)$attr['custom'], $this->groupLevel));
            }
        }

        $this->groupLevel++;
        $closure($this);
        $groupLevel = $this->groupLevel--;

        unset(
            $this->prefix[$groupLevel],
            $this->custom[$groupLevel]
        );
    }

    /**
     * get routeTree obj
     * @return RouteTree
     */
    public function routeTree() : RouteTree
    {
        return $this->routeTree;
    }

    /**
     * add route mapping
     * @param $method
     * @param string $route
     * @param $handler
     * @return $this
     */
    public function addRoute($method, string $route, $handler)
    {
        $routeLeaf = new RouteLeaf();
        $routeLeaf->setUri($this->mergePrefix($route));
        $routeLeaf->setCustom($this->mergeCustom([]));
        $routeLeaf->setMethod($method);
        $routeLeaf->setHandler($handler);

        $this->routeTree()->insert($routeLeaf);

        return $this;
    }

    /**
     * search route
     * @param string $route
     * @return RouteLeaf|null
     */
    public function dispatch(string $route)
    {
        return $this->routeTree()->search($route);
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
            return $this->addRoute(strtoupper($method), ...$arguments);
        }

        throw new RouteException('Call to undefined method ' . __CLASS__ . '::' . $method . '()');
    }

    /**
     * @param string $prefix
     * @param int|null $level
     * @return string
     */
    private function mergePrefix(string $prefix, int $level = null)
    {
        if (is_null($level)) {
            if ($this->prefix) {
                return join('/', $this->prefix) . '/' . trim($prefix, '/');
            }

            return '/' . trim($prefix, '/');
        }

        return ($this->prefix[$level] ?? '') . '/' . trim($prefix, '/');
    }

    /**
     * @param array $custom
     * @param int|null $level
     * @return array
     */
    private function mergeCustom(array $custom, int $level = null)
    {
        if (is_null($level)) {
            if ($this->custom) {
                return array_merge_recursive(...$this->custom);
            }

            return $custom;
        }

        return array_merge_recursive($this->custom[$level] ?? [], $custom);
    }
}
