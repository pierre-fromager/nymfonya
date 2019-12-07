<?php

use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Headers;
use App\Middlewares\Cors;
use App\Middlewares\Jwt;
use App\Middlewares\Restful;
use App\Middlewares\After;

return [
    Cors::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [],
        'headers' => [
            Headers::CONTENT_TYPE => 'application/json; charset=utf-8',
            Headers::HEADER_ACA_ORIGIN  =>  '*',
            Headers::HEADER_ACA_CREDENTIALS => 'true',
            Headers::HEADER_ACA_METHODS => implode(',', [
                Request::METHOD_GET, Request::METHOD_POST,
                Request::METHOD_PUT, Request::METHOD_DELETE,
                Request::METHOD_OPTIONS
            ]),
            Headers::HEADER_ACA_HEADERS =>  implode(',', [
                'Access-Control-Allow-Headers', 'Origin', 'Accept',
                'X-Requested-With', 'Content-Type',
                'Access-Control-Request-Method',
                'Access-Control-Request-Headers',
                'Access-Control-Allow-Origin',
                'Authorization'
            ])
        ]
    ],
    Restful::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [
            '/^(!restful.*)$/',
            '/^(test)\/(.*)$/',
            '/^(auth)\/(.*)$/',
            '/^(stat)\/(.*)$/',
            '/^(metro)\/(.*)$/'
        ],
    ],
    Jwt::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [
            'auth/login', 'stat/opcache',
            'test/pokerelay', 'stat/redis',
            'metro/lines', 'metro/stations',
        ],
    ],
    After::class => [
        'enabled' => true,
        'prefix' => '/api/v1/',
        'exclude' => [],
    ],
];
