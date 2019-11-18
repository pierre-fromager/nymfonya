<?php
return [
    'request' => [
        'scheme' => 'https',
        'hostname' => '',
    ],
    'jwt' => [
        // Secret for signing the JWT's, I suggest generate it with base64_encode(openssl_random_pseudo_bytes(64))
        'secret' => 'qACAXC/FnPbKk2JYQ1/LLFSYcJrmawZ8YAvC2g7dE+z52VWY+u+ziUPC5wp1cLhai1bo5kpFxWFMZXdtci9r6Q==',
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
];
