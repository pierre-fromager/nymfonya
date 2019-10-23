<?php

return [
    \App\Config::class => [\App\Config::ENV_CLI, __DIR__ . '/../'],
    \App\Http\Request::class => [],
    \App\Http\Response::class => [],
    \App\Http\Routes::class => [include(__DIR__ . '/routes.php')],
    \App\Http\Router::class => [
        \App\Http\Routes::class,
        \App\Http\Request::class
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
    ]
];
