<?php
namespace Air\Test;

use Air\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    /**
     * @var Route
     */
    private $route;

    protected function setUp(): void
    {
        $this->route = new Route();
    }

    protected function tearDown(): void
    {
        $this->route = null;
    }
    
    public function testAddRoute()
    {
        $this->route
            ->prefix('/test')
            ->custom(['np' => 'Test'])
            ->group(function (Route $route) {
                $route->get('/msg/a', 'Message\ABC@index');
                $route->get('/msg/{mid}', 'Message\ABC@index');
                $route->cli('/msg/{mid}', 'Message\ABC@index');
                $route->put('/msg/{mid}', 'Message\ABC@index');
                $route->post('/msg/{mid}', 'Message\ABC@index');

                $route->addRoute(['PUT', 'POST'], '/msg/{mid}', 'Replace@index');
            });

        //print_r($this->route->routeTree()->getLeafs());

        $result = $this->route->dispatch('/test/msg/123123');
        $result2 = $this->route->dispatch('/test/msg/a');
        $this->assertIsObject($result);
        $this->assertIsObject($result2);

        print_r($result2);

        $this->assertEmpty($this->route->dispatch('test/test/msg/123123'));
    }
}
