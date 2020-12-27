<?php

declare(strict_types=1);

namespace App\Middlewares;

use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Interfaces\MiddlewareInterface;
use Nymfonya\Component\Container;

/**
 * App\Middleware\Restful
 *
 * Patch kernel action from request method
 */
class Restful implements MiddlewareInterface
{
    use \App\Middlewares\Reuse\TInit;

    const _SIGN = 'X-Middleware-Restful';
    const METHOD_ACTIONS = [
        Request::METHOD_GET => 'index',
        Request::METHOD_POST => 'store',
        Request::METHOD_PUT => 'update',
        Request::METHOD_PATCH => 'update',
        Request::METHOD_DELETE => 'delete',
        Request::METHOD_OPTIONS => 'preflight',
    ];

    /**
     * peel
     *
     * @param Container $container
     * @param \Closure $next
     * @return \Closure
     */
    public function peel(Container $container, \Closure $next)
    {
        $this->init($container);
        $this->process();
        return $next($container);
    }

    /**
     * process
     *
     */
    private function process()
    {
        if ($this->enabled) {
            $this->response->getHeaderManager()->add(
                self::_SIGN,
                strval(microtime(true))
            );
            if ($this->required()) {
                $pureCa = preg_replace('/\?.*/', '', $this->caUri()) . '/';
                $caFrags = explode('/', $pureCa);
                $controller = $caFrags[0];
                $met = $this->request->getMethod();
                $action = self::METHOD_ACTIONS[$met];
                $this->kernel->setAction([$controller, $action]);
            }
        }
    }

    /**
     * required
     *
     * @return boolean
     */
    protected function required(): bool
    {
        return (!$this->isExclude() && $this->requestUriPrefix() === $this->prefix);
    }

    /**
     * check exclusion uri fragment from exclude regexps.
     * note that negate with ! means allow.
     *
     * @return Boolean
     */
    protected function isExclude(): bool
    {
        $reqFrag = $this->caUri();
        $countExc = count($this->exclude);
        for ($i = 0; $i < $countExc; $i++) {
            $matches = [];
            $match = preg_match($this->exclude[$i], $reqFrag, $matches);
            if ($match) {
                return true;
            }
        }
        return false;
    }

    /**
     * return controller action from uri
     *
     * @return string
     */
    protected function caUri(): string
    {
        return str_replace($this->prefix, '', $this->request->getUri());
    }

    /**
     * uriPrefix
     *
     * @return string
     */
    protected function requestUriPrefix(): string
    {
        return substr($this->request->getUri(), 0, strlen($this->prefix));
    }
}
