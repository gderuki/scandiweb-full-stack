<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Bootstrap.php';

use Controllers\GraphQLController;
use Utils\RateLimiter;

$redis = new Redis();
$redis->connect('redis', 6379);

$rateLimiter = new RateLimiter($redis, 9001, 1800);

$identifier = $_SERVER['REMOTE_ADDR'];

if ($rateLimiter->isLimited($identifier)) {
    http_response_code(429);
    die('Rate limit exceeded. Please try again later.');
}

GraphQLController::init($serviceLocator);

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->post('/', [GraphQLController::class, 'handle']);
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        break;
}
