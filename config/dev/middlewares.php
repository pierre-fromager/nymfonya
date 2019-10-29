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
            'auth/login',
        ],
    ],
    App\Middlewares\After::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [],
    ],
];
