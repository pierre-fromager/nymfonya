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
    'middlewares' => include 'dev/middlewares.php',
    'router' => [
        'unroutable' => '!\.(ico|xml|txt|avi|htm|zip|js|ico|gif|jpg|JPG|png|css|swf|flv|m4v|mp3|mp4|ogv|webm|woff)$'
    ],
    'routes' => include 'dev/routes.php',
    'accounts' => include 'dev/accounts.php',
    'services' => include 'dev/services.php',
    'logger' => [
        'path' => '/../log/app.log',
        'name' => 'skypass'
    ],
    'redis' => include 'dev/redis.php',
    'db' => include 'dev/db.php',
    'auth' => ['adapter' => App\Component\Auth\Adapters\Repository::class],
    'mailer' => include 'dev/mailer.php'
];
