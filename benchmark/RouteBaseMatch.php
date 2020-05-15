<?php
declare(strict_types=1);

namespace Air\Benchmark;

use Air\Routing\Route;

/**
 * Class RouteMatch
 * @package Air\Benchmark
 */
class RouteBaseMatch extends RouteBase
{
    /**
     * @param Route $route
     * @return mixed|void
     */
    protected function addRoutes(Route $route)
    {
        $route->addCustomData(['define' => 't1'])->group(static function(Route $router) {
            $router->get('/market', 'Market@all');
            $router->get('/market/hot', 'Market@hot');
            $router->get('/market/stat', 'Market@stat');
            $router->get('/market/gears', 'Market@gears');
            $router->get('/market/{id}/{id}/{id}/{id}/{id}/{id}', 'Market@detail');

            $router->put('/market/tw', 'Market\Sync@twn');
            $router->put('/market/ib', 'Market\Sync@ibn');

            $router->get('/graph/{minute}', 'Graph\Minute@data');
            $router->get('/graph/kline', 'Graph\KLine@data');

            $router->put('/message/news', 'Message\News@insert');
            $router->get('/message/news', 'Message\News@list');
            $router->get('/message/details', 'Message\News@details');
            $router->get('/message/{banners}/{d}', 'Message\News@banners');
            $router->get('/message/notice/a/b/c/d/e/d/s/d/s/s/s/s/sssss/d/d/e/s/s', 'Message\Notice@select');
        });
    }

    /**
     * @return iterable
     */
    public function provideStaticRoute(): iterable
    {
        yield 'first' => ['route' => '/message/notice/a/b/c/d/e/d/s/d/s/s/s/s/sssss/d/d/e/s/s'];

        yield 'last' => ['route' => '/message/news'];

        yield 'Unknown' => ['route' => '/message/notice/a/b/c/d/e/d/s/d/s/s/s/s'];
    }

    /**
     * @return iterable
     */
    public function provideDynamicRoute(): iterable
    {
        yield 'first' => ['route' => '/market/a/b/b/r4/cc/cc'];

        yield 'last' => ['route' => '/message/banner/c'];

        yield 'Unknown' => ['route' => '/market/ddd/dd/dd/cc/dd'];
    }

    /**
     * @return iterable
     */
    public function provideOtherRoute(): iterable
    {
        yield 'first' => ['route' => '/a'];

        yield 'last' => ['route' => '/b'];
    }
}
