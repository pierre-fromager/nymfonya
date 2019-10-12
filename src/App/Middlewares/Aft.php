<?php

namespace App\Middlewares;

use App\Http\Interfaces\Middleware\ILayer;

class Aft implements ILayer
{
    public function peel($object, \Closure $next)
    {
        $response = $next($object);
        echo 'after ' . microtime(true) . "\n";
        //var_dump($object);die;
        return $response;
    }
}
