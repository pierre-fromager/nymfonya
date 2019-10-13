<?php

namespace App\Middlewares;

use App\Http\Interfaces\Middleware\ILayer;
use App\Container;

class Aft implements ILayer
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
        $response = $next($object);
        echo 'after ' . microtime(true) . "\n";
        //var_dump($object);die;
        return $response;
    }
}
