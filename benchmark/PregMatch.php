<?php
namespace Air\Benchmark;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Subject;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;

/**
 * @Warmup(2)
 * @Revs(1000)
 * @Iterations(5)
 *
 * Class PregMatch
 * @package Air\Benchmark
 */
class PregMatch
{
    /**
     * @Subject()
     */
    public function matchGroupPos()
    {
        preg_match(
            '~^(?:/page/([a-zA-Z0-9\\-]+)|/blog/post/([a-zA-Z0-9\\-]+)|/shop/category/search/([a-zA-Z]+)\:([^/]+)|/shop/category/(\d+)|/shop/category/(\d+)/product|/shop/category/(\d+)/product/search/([a-zA-Z]+)\:([^/]+)|/shop/product/search/([a-zA-Z]+)\:([^/]+)|/shop/product/(\\d+)|/admin/product/(\d+)|/admin/product/(\d+)/edit|/admin/category/(\d+)|/admin/category/(\d+)/edit|/page/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+))$~',
            '/page/category/123/123/123/123/123/123/123/123',
            $matches
        );

        //print_r($matches);
    }

    /**
     * @Subject()
     */
    public function matchGroupCount()
    {
        preg_match(
            '~^(?|/page/([a-zA-Z0-9\\-]+)|/blog/post/([a-zA-Z0-9\\-]+)()|/shop/category/search/([a-zA-Z]+)\:([^/]+)()|/shop/category/(\d+)()()()|/shop/category/(\d+)/product()()()()|/shop/category/(\d+)/product/search/([a-zA-Z]+)\:([^/]+)()()()|/shop/product/search/([a-zA-Z]+)\:([^/]+)()()()()()|/shop/product/(\d+)()()()()()()()|/admin/product/(\d+)()()()()()()()()|/admin/product/(\d+)/edit()()()()()()()()()|/admin/category/(\d+)()()()()()()()()()()|/admin/category/(\d+)/edit()()()()()()()()()()()|/page/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)/([a-zA-Z0-9\\-]+)()()()())$~',
            '/page/category/123/123/123/123/123/123/123/123',
            $matches);
    }
}
