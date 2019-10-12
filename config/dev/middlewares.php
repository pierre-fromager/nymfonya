<?php

return [
    'cors' => App\Middlewares\Cors::class,
    'jwt' => App\Middlewares\Jwt::class,
    'aft' => App\Middlewares\After::class,
];
