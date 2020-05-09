<?php
namespace Air\Router\Test;

use Air\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * @var Route
     */
    private $router;

    protected function setUp(): void
    {
        $this->router = new Route();
    }

    protected function tearDown(): void
    {
        $this->router = null;
    }

    public function testAddRoute()
    {
        $this->assertEmpty(
            $this->router
                ->prefix('test')
                ->config(['np' => 'Test'])
                ->group(function (Route $route) {
                    $route
                        ->prefix('test2')
                        ->config(['np' => 'Test2', 'md' => ['c', 'd']])
                        ->group(function (Route $route) {
                            $route->addRoute(['GET', 'PUT'], '/msg/{mid}', 'Message\ABC@index');
                        });

                    $route->any('/b/c/{uid}', 'A@a');
                })
        );

//        print_r($this->router->getRouteTree()->getLeafs());
        var_dump($this->router->dispatch('test/test2/msg/123123'));
    }
}
