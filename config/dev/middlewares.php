<?php

return [
    App\Middlewares\Cors::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [],
    ],
    App\Middlewares\Jwt::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [
            'auth/login', 'stat/opcache', 'stat/filecache',
            'restful', 'restful/index', 'test/pokerelay'
        ],
    ],
    App\Middlewares\Restful::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [
            '/^(!restful.*)$/',
            '/^(test)\/(.*)$/',
            '/^(auth)\/(.*)$/',
            '/^(stat)\/(.*)$/'
        ],
    ],
    App\Middlewares\After::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [],
    ],
];
