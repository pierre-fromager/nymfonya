<?php

return [
    \App\Middlewares\Cors::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => ['auth/login', 'user/register'],
    ],
    \App\Middlewares\Jwt::class => [
        'enabled' => false,
        'prefix' => '/api/v1/',
        'exclude' => ['auth/login', 'user/register'],
    ],
    \App\Middlewares\After::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => ['auth/login', 'user/register'],
    ],
];
