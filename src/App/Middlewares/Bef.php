<?php

namespace App\Middlewares;

use App\Http\Interfaces\Middleware\ILayer;

class Bef implements ILayer
{
    public function peel($object, \Closure $next)
    {
        echo 'before ' . microtime(true) . "\n";
        return $next($object);
    }
}
