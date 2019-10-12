<?php

namespace App\Middlewares;

use App\Http\Interfaces\Middleware\ILayer;
use App\Container;
use App\Http\Headers;

class After implements ILayer
{
    use \App\Middlewares\Reuse\TInit;

    const _SIGN = 'X-Middleware-After';

    /**
     * peel poil
     *
     * @param Container $container
     * @param \Closure $next
     * @return void
     */
    public function peel(Container $container, \Closure $next)
    {
        $r = $next($container);
        $this->init($container);
        $this->logger->debug('After middleware');
        $this->response->getHeaderManager()->add(
            self::_SIGN,
            microtime(true)
        );
        return $r;
    }
}
