<?php

namespace App\Http\Interfaces\Middleware;

use \Closure;
use App\Container;

interface ILayer
{
    public function peel(Container $container, Closure $next);
}
