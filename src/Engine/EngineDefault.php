<?php
declare(strict_types=1);

namespace Air\Routing\Engine;

use Air\Routing\EngineInterface;
use Air\Routing\Exception\RouteException;

/**
 * Class EngineDefault
 * @package Air\Routing\Engine
 */
class EngineDefault extends EngineAbstract implements EngineInterface
{
    /**
     * @var array
     */
    private $data;

    private const LEAF = '##LEAF##';

    private const SEPARATOR = '/';

    private const PARAMETER = '*';

    /**
     * EngineDefault constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $methods
     * @param string $uri
     * @param array $userData
     * @throws RouteException
     */
    public function insert(array $methods, string $uri, array $userData): void
    {
        $data = &$this->data;
        $tokens = $this->parse($uri);

        if (is_array($tokens)) {
            foreach ($tokens as $token) {
                if (false !== strpos($token, '{')) {
                    $matches[substr($token, 1, -1)] = null;

                    $token = static::PARAMETER;
                }

                if (!isset($tree[$token])) {
                    $data[$token] = [];
                }

                $data = &$data[$token];
            }

            $map = static::LEAF;
        } else {
            $map = $tokens;
        }

        $handler = [];
        if (isset($data[$map])) {
            $handler = $data[$map]['handler'];
        }

        foreach ($methods as $method) {
            if (isset($handler[$method])) {
                throw new RouteException(sprintf(
                    'Cannot register two routes matching "%s" for method "%s"',
                    $uri,
                    $method
                ));
            }

            $handler[$method] = $userData['handler'];
        }

        $data[$map] = [
            'uri' => $uri,
            'matches' => $matches ?? [],
            'handler' => $handler,
            'custom_data' => $userData['custom_data']
        ];
    }

    /**
     * @param string $route
     * @return array
     */
    public function search(string $route): array
    {
        $data = $this->getData();

        if (!isset($data[$route])) {
            foreach ($this->split($route) as $leaf) {
                if (isset($data[$leaf])) {
                    $data = $data[$leaf];
                } elseif (isset($data[static::PARAMETER])) {
                    $data = $data[static::PARAMETER];
                    $matches[] = $leaf;
                } else {
                    return [];
                }
            }

            if (!isset($tree[static::LEAF])) {
                return [];
            }

            $map = static::LEAF;
        } else {
           $map = $route;
        }

        foreach ($data[$map]['matches'] as $key) {
            $data[$map]['matches'][$key] = array_shift($matches);
        }

        return $data[$map];
    }

    /**
     * @param string $route
     * @return string
     */
    protected function parse(string $route)
    {
        if (!preg_match('/\{[^\/]+\}/i', $route)) {
            return $route;
        }

        return $this->split($route);
    }

    /**
     * @param string $string
     * @return false|string[]
     */
    private function split(string $string)
    {
        if ($string === '/') {
            return [$string];
        }

        return explode(static::SEPARATOR, trim($string, static::SEPARATOR));
    }
}
