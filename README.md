Route
=============
A simple restful style request route

Install
-------
To install with composer
```sh
composer require john-jun/route
```

Test
-----
```sh
composer test
```

Usage
-----
```php
$route = new Air\Routing\Route();

//add route
$route->get('/get', '{className}@{method}');
$route->cli('/cli', '{className}@{method}');
$route->put('/put', '{className}@{method}');
$route->head('/head', '{className}@{method}');
$route->post('/post', '{className}@{method}');
$route->patch('/patch', '{className}@{method}');
$route->delete('/delete', '{className}@{method}');
$route->options('/options', '{className}@{method}');

//add route group
$route->group('prefix', static function(Air\Routing\Route $route) {
    $route->get('/get', '{className}@{method}');
    $route->cli('/cli', '{className}@{method}');
    $route->put('/put', '{className}@{method}');
    $route->head('/head', '{className}@{method}');
    $route->post('/post', '{className}@{method}');
    $route->patch('/patch', '{className}@{method}');
    $route->delete('/delete', '{className}@{method}');
    $route->options('/options', '{className}@{method}');
});

print_r($route->dispatch('/get'));
print_r($route->dispatch('/prefix/get'));
```