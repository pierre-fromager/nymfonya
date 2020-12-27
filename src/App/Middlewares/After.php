<?php

declare(strict_types=1);

namespace App\Middlewares;

use Nymfonya\Component\Http\Interfaces\MiddlewareInterface;
use Nymfonya\Component\Container;

class After implements MiddlewareInterface
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
            strval(microtime(true))
        );
        return $res;
    }
}
