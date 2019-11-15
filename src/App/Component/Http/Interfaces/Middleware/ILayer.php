<?php

namespace App\Component\Http\Interfaces\Middleware;

use \Closure;
use App\Container;

interface ILayer
{
    const _EXCLUDE = 'exclude';
    const _INCLUDE = 'include';
    const _PREFIX = 'prefix';

    public function peel(Container $container, Closure $next);
}
