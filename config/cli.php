<?php

return [
    'request' => [
        'scheme' => 'https',
        'hostname' => '',
    ],
    'jwt' => [
        // Secret for signing the JWT's, I suggest generate it with base64_encode(openssl_random_pseudo_bytes(64))
        'secret' => '1+OTJaS7UF3jq263z1eYB1ktIUO09XwDAfm61woFYA8NTnJGZya5/HZYAonT1UCQhiCYdHAw0xxMAuhcDijJnw==',
        'algorithm' => 'HS512',
    ],
    'middlewares' => include 'cli/middlewares.php',
    'router' => [
        'unroutable' => '!\.(ico|xml|txt|avi|htm|zip|js|ico|gif|jpg|JPG|png|css|swf|flv|m4v|mp3|mp4|ogv|webm|woff)$'
    ],
    'routes' => include 'cli/routes.php',
    'accounts' => include 'cli/accounts.php',
    'services' => include 'cli/services.php',
    'logger' => [
        'path' => '/../log/console.log',
        'name' => 'skypass'
    ],
    'redis' => include 'cli/redis.php',
    'db' => include 'cli/db.php',
    'auth' => [
        'adapter' => \App\Component\Auth\Adapters\File::class
    ],
    'mailer' => include 'cli/mailer.php'
];
