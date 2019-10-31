<?php

return [
    \App\Middlewares\Cors::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => ['stat/filecache'],
    ],
    \App\Middlewares\Jwt::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [
            'auth/login', 'test/pokerelay',
        ],
    ],
    App\Middlewares\Restful::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [
            '/^(!restful.*)$/',
            '/^(test)\/(.*)$/',
            '/^(auth)\/(.*)$/',
            '/^(stat)\/(.*)$/',
            '/^(config)\/(.*)$/'
        ],
    ],
    \App\Middlewares\After::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => ['auth/login'],
    ],
];
