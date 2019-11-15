<?php

namespace App\Middlewares;

use App\Component\Http\Interfaces\Middleware\ILayer;
use App\Component\Container;
use App\Component\Http\Headers;

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
        $res = $next($container);
        $this->init($container);
        $this->logger->debug('After middleware');
        $this->response->getHeaderManager()->add(
            self::_SIGN,
            microtime(true)
        );
        return $res;
    }
}
