<?php

namespace App\Middlewares;

use App\Http\Interfaces\Middleware\ILayer;
use App\Container;

class Bef implements ILayer
{
    /**
     * peel poil
     *
     * @param Container $object
     * @param \Closure $next
     * @return void
     */
    public function peel(Container $object, \Closure $next)
    {
        echo 'before ' . microtime(true) . "\n";
        return $next($object);
    }
}
