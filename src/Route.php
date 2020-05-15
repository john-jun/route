<?php
declare(strict_types=1);

namespace Air\Routing;

use Air\Routing\Engine\EngineDefault;
use BadMethodCallException;
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
 * @method Route options(string $uri, string $handle)
 */
class Route implements RouteInterface
{
    /**
     * @var array
     */
    private $prefix;

    /**
     * @var EngineDefault
     */
    private $engine;

    /**
     * @var array
     */
    private $customData;

    /**
     * @var int
     */
    private $groupLevel = 0;

    /**
     * Route constructor.
     *
     * @param EngineInterface|null $engine
     */
    public function __construct(?EngineInterface $engine = null)
    {
        $this->engine = $engine ?: new EngineDefault();
    }

    /**
     * Add route group
     *
     * @param $args
     */
    public function group($args): void
    {
        $args = func_get_args();

        $closure = array_pop($args);
        if (!$closure instanceof Closure) {
            return;
        }

        $attr = array_shift($args);
        if (is_string($attr)) {
            $this->addPrefix($this->mergePrefix($attr, $this->groupLevel));
        } elseif (is_array($attr)) {
            if (isset($attr['prefix'])) {
                $this->addPrefix($this->mergePrefix($attr['prefix'], $this->groupLevel));
            }

            if (isset($attr['custom_data'])) {
                $this->addCustomData($this->mergeCustomData((array)$attr['custom_data'], $this->groupLevel));
            }
        }

        $this->groupLevel++;
        $closure($this);
        $groupLevel = $this->groupLevel--;

        unset(
            $this->prefix[$groupLevel],
            $this->customData[$groupLevel]
        );
    }

    /**
     * @return EngineInterface
     */
    public function getEngine() : EngineInterface
    {
        return $this->engine;
    }

    /**
     * Add Route
     * @param $method
     * @param string $route
     * @param $handler
     */
    public function addRoute($method, string $route, $handler)
    {
        $this->getEngine()->insert(
            (array)$method,
            $this->mergePrefix($route),
            [
                'handler' => $handler,
                'custom_data' => $this->mergeCustomData([])
            ]
        );
    }

    /**
     * Search route
     *
     * @param string $route
     * @param null $method
     * @return array
     */
    public function dispatch(string $route, $method = null)
    {
        return $this->getEngine()->search($route);
    }

    /**
     * @param $method
     * @param $arguments
     */
    public function __call($method, $arguments)
    {
        if (!in_array($method, ['get', 'any', 'cli', 'put', 'head', 'post', 'patch', 'delete', 'options'])) {
            throw new BadMethodCallException('Call to undefined method ' . __CLASS__ . '::' . $method . '()');
        }

        $this->addRoute(strtoupper($method), ...$arguments);
    }

    /**
     * Set prefix
     *
     * @param string $prefix
     * @return static
     */
    public function addPrefix(string $prefix)
    {
        $this->prefix[$this->groupLevel] = $prefix;

        return $this;
    }

    /**
     * Set customData
     *
     * @param array $customData
     * @return $this
     */
    public function addCustomData(array $customData)
    {
        $this->customData[$this->groupLevel] = $customData;

        return $this;
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
                return join('/', $this->prefix) . $prefix;
            }

            return $prefix;
        }

        return ($this->prefix[$level] ?? '') . $prefix;
    }

    /**
     * @param array $customData
     * @param int|null $level
     * @return array
     */
    private function mergeCustomData(array $customData, int $level = null)
    {
        if (is_null($level)) {
            if ($this->customData) {
                return array_merge_recursive(...$this->customData);
            }

            return $customData;
        }

        return array_merge_recursive($this->customData[$level] ?? [], $customData);
    }
}
