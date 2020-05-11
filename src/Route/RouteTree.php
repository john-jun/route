<?php
declare(strict_types=1);

namespace Air\Routing\Route;

/**
 * Class RouteTree
 * @package Air\Router\Route
 */
class RouteTree
{
    /**
     * @var array
     */
    private $tree = [];

    private const LEAF = '#leaf#';

    private const SEPARATOR = '/';

    private const PARAMETER = '*';

    /**
     * @return array
     */
    public function getLeafs() : array
    {
        return $this->tree;
    }

    /**
     * @param array $tree
     */
    public function setLeafs(array $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @param RouteLeaf $routeLeaf
     */
    public function insert(RouteLeaf $routeLeaf)
    {
        $tree = &$this->tree;
        $matches = [];

        foreach ($this->split($routeLeaf->getUri()) as $token) {
            if (strpos($token, '{') !== false) {
                $matches[] = substr($token, 1, -1);

                $token = static::PARAMETER;
            }

            if (!isset($tree[$token])) {
                $tree[$token] = [];
            }

            $tree = &$tree[$token];
        }

        if (isset($tree[static::LEAF])) {
            $oldRouteLeaf = clone $routeLeaf;
            $oldRouteLeaf->setLeafData($tree[static::LEAF]);

            $handler = [];
            foreach ($oldRouteLeaf->getMethod() as $method) {
                $handler[$method] = $oldRouteLeaf->getHandler()[$method] ?? $oldRouteLeaf->getHandler();
            }

            foreach ($routeLeaf->getMethod() as $method) {
                $handler[$method] = $routeLeaf->getHandler();

                if (!in_array($method, $oldRouteLeaf->getMethod())) {
                    $oldRouteLeaf->setMethod(array_merge($oldRouteLeaf->getMethod(), [$method]));
                }
            }

            $oldRouteLeaf->setHandler($handler);
            $routeLeaf = $oldRouteLeaf;
        }

        $routeLeaf->setMatches($matches);
        $tree[static::LEAF] = $routeLeaf->getLeafData();
    }

    /**
     * @param string $leafs
     * @return RouteLeaf|null
     */
    public function search(string $leafs)
    {
        $tree = $this->getLeafs();
        $leafs = $this->split($leafs);

        foreach ($leafs as $leaf) {
            if (isset($tree[$leaf])) {
                $tree = $tree[$leaf];
            } elseif (isset($tree[static::PARAMETER])) {
                $tree = $tree[static::PARAMETER];
                $matches[] = $leaf;
            } else {
                return null;
            }
        }

        if (isset($tree[static::LEAF])) {
            $routeLeaf = new RouteLeaf();
            $routeLeaf->setLeafData($tree[static::LEAF]);

            $matchesArgs = [];
            foreach ($routeLeaf->getMatches() as $key) {
                $matchesArgs[$key] = array_shift($matches);
            }
            $routeLeaf->setMatches($matchesArgs);

            return $routeLeaf;
        }

        return null;
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
