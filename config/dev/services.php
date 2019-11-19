<?php

use App\Component\Config;
use App\Component\Http\Request;
use App\Component\Http\Response;
use App\Component\Http\Routes;
use App\Component\Http\Router;

return [
    Config::class => [Config::ENV_DEV, __DIR__ . '/../'],
    Request::class => [],
    Response::class => [],
    Routes::class => [include(__DIR__ . '/routes.php')],
    Router::class => [
        Routes::class,
        Request::class
    ],
    \Monolog\Handler\RotatingFileHandler::class => [
        realpath(__DIR__ . '/../../log') . '/devapp.txt',
        0,
        \Monolog\Logger::DEBUG,
        true,
        0664
    ],
    \Monolog\Logger::class => [
        Config::_NAME,
        [\Monolog\Handler\RotatingFileHandler::class],
        [\Monolog\Processor\WebProcessor::class]
    ],
    \App\Component\Cache\Redis\Adapter::class => [Config::class]
];
