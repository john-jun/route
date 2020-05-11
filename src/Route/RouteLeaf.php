<?php
declare(strict_types=1);

namespace Air\Routing\Route;

/**
 * Class RouteLeaf
 * @package Air\Router\Route
 */
class RouteLeaf
{
    /**
     * @var array
     */
    private $leafData;

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->leafData['uri'];
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri): void
    {
        $this->leafData['uri'] = $uri;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return (array)$this->leafData['method'];
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method): void
    {
        $this->leafData['method'] = $method;
    }

    /**
     * @return array
     */
    public function getCustom()
    {
        return (array)$this->leafData['custom'];
    }

    /**
     * @param $addition
     */
    public function setCustom($addition): void
    {
        $this->leafData['custom'] = $addition;
    }

    /**
     * @return array
     */
    public function getMatches()
    {
        return (array)($this->leafData['matches'] ?? []);
    }

    /**
     * @param $matches
     */
    public function setMatches($matches): void
    {
        $this->leafData['matches'] = $matches;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->leafData['handler'];
    }

    /**
     * @param mixed $handler
     */
    public function setHandler($handler): void
    {
        $this->leafData['handler'] = $handler;
    }

    /**
     * @return array
     */
    public function getLeafData() : array
    {
        return $this->leafData;
    }

    /**
     * @param array $leafData
     */
    public function setLeafData(array $leafData): void
    {
        $this->leafData = $leafData;
    }
}
