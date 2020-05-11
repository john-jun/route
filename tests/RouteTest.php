<?php
namespace Air\Routing\Test;

use Air\Routing\Exception\RouteException;
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
            ->prefix('test')
            ->custom(['np' => 'Test'])
            ->group(['prefix' => 'test2'], function (Route $route) {
                $route->get('/msg/{mid}', 'Message\ABC@index');
                $route->cli('/msg/{mid}', 'Message\ABC@index');
                $route->put('/msg/{mid}', 'Message\ABC@index');
                $route->post('/msg/{mid}', 'Message\ABC@index');

                $route->addRoute(['PUT', 'POST'], '/msg/{mid}', 'Replace@index');
            });

        $result = $this->route->dispatch('test/test2/msg/123123');

        $this->assertEmpty($this->route->dispatch('test/test/msg/123123'));
        $this->assertIsObject($result);
    }
}
