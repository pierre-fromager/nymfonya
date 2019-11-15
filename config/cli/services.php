<?php

return [
    \App\Config::class => [\App\Config::ENV_CLI, __DIR__ . '/../'],
    \App\Component\Http\Request::class => [],
    \App\Component\Http\Response::class => [],
    \App\Component\Http\Routes::class => [include(__DIR__ . '/routes.php')],
    \App\Component\Http\Router::class => [
        \App\Component\Http\Routes::class,
        \App\Component\Http\Request::class
    ],
    \Monolog\Handler\RotatingFileHandler::class => [
        realpath(__DIR__ . '/../../log') . '/console.txt',
        0,
        \Monolog\Logger::DEBUG,
        true,
        0664
    ],
    \Monolog\Logger::class => [
        \App\Config::_NAME,
        [\Monolog\Handler\RotatingFileHandler::class],
        [\Monolog\Processor\WebProcessor::class]
    ],
    \App\Component\Cache\Redis\Adapter::class => [\App\Config::class]
];
