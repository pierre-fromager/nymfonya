<?php

use App\Http\Request;
use App\Http\Headers;

return [
    \App\Middlewares\Cors::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => ['stat/filecache'],
        'headers' => [
            Headers::HEADER_ACA_ORIGIN  =>  '*',
            Headers::HEADER_ACA_CREDENTIALS => 'true',
            Headers::HEADER_ACA_METHODS => implode(',', [
                Request::METHOD_GET, Request::METHOD_POST,
                Request::METHOD_PUT, Request::METHOD_DELETE,
                Request::METHOD_OPTIONS
            ]),
            Headers::HEADER_ACA_HEADERS =>  implode(',', [
                'Access-Control-Allow-Headers', 'Origin', 'Accept',
                'X-Requested-With', 'Content-Type', 'Access-Control-Request-Method',
                'Access-Control-Request-Headers', 'Access-Control-Allow-Origin',
                'Authorization'
            ])
        ]
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
