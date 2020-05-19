<?php
declare(strict_types=1);

namespace Air\Routing\Benchmark;

use Air\Routing\Route;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;

/**
 * @Warmup(2)
 * @Revs(1000)
 * @Iterations(5)
 * @BeforeMethods({"initializeRoute"})
 */
abstract class RouteBase
{
    /**
     * @var Route
     */
    private $route;

    public function initializeRoute()
    {
        $this->route = new Route();
        $this->addRoutes($this->route);
    }

    /**
     * @param Route $route
     * @return mixed
     */
    abstract protected function addRoutes(Route $route);

    /**
     * @return iterable
     */
    abstract public function provideStaticRoute(): iterable;

    /**
     * @return iterable
     */
    abstract public function provideDynamicRoute(): iterable;

    /**
     * @return iterable
     */
    abstract public function provideOtherRoute(): iterable;

    /**
     * @ParamProviders({"provideStaticRoute"})
     * @param array $route
     */
    public function benchStaticRoute(array $route)
    {
        $this->runRoute($route);
    }

    /**
     * @ParamProviders({"provideDynamicRoute"})
     * @param array $route
     */
    public function benchDynamicRoutes(array $route)
    {
        $this->runRoute($route);
    }

    /**
     * @ParamProviders({"provideOtherRoute"})
     * @param array $route
     */
    public function benchOtherRoutes(array $route)
    {
        $this->runRoute($route);
    }

    /**
     * @param array $route
     */
    private function runRoute(array $route): void
    {
        $this->route->dispatch($route['route']);
    }
}
