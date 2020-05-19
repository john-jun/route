<?php
namespace Air\Routing\Test;

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
        $this->route->group(['custom_data' => ['define' => 'NP']], static function(Route $router) {
            $router->get('/market', 'Market@all');
            $router->get('/market/hot', 'Market@hot');
            $router->get('/market/stat', 'Market@stat');
            $router->get('/market/gears', 'Market@gears');
            $router->get('/market/{id}/{id}/{id}/{id}/{id}/{id}', 'Market@detail');

            $router->put('/market/tw', 'Market\Sync@twn');
            $router->put('/market/ib', 'Market\Sync@ibn');

            $router->get('/graph/{minute}', 'Graph\Minute@data');
            $router->get('/graph/kline', 'Graph\KLine@data');

            $router->get('/message/notice/a/b/c/d/e/d/s/d/s/s/s/s/sssss/d/d/e/s/s', 'Message\Notice@select');
            $router->put('/message/news', 'Message\News@insert');
            $router->get('/message/news', 'Message\News@list');
            $router->get('/message/details', 'Message\News@details');
            $router->get('/message/{banners}/{d}', 'Message\News@banners');
        });
    }

    protected function tearDown(): void
    {
        $this->route = null;
    }
    
    public function testMatch()
    {
        print_r($this->route->getEngine()->getData());

        $result = $this->route->dispatch('/market/tw');
        $result2 = $this->route->dispatch('/message/news');
        $this->assertIsArray($result);
        $this->assertIsArray($result2);

//        print_r($result);
//        print_r($result2);

        $this->assertEmpty($this->route->dispatch('test/test/msg/123123'));
    }
}
