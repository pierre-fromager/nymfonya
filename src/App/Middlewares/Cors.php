<?php

namespace App\Middlewares;

use App\Http\Request;
use App\Http\Response;
use App\Http\Headers;
use App\Http\Interfaces\Middleware\ILayer;
use App\Container;

/**
 * App\Middleware\Cors
 *
 * Patch initial response to accept CORS requests
 */
class Cors implements ILayer
{

    use \App\Middlewares\Reuse\TInit;

    const _SIGN = 'X-Middleware-Cors';

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
                microtime(true)
            );
            if ($this->required()) {
                $this->response
                    ->setCode(Response::HTTP_NOT_FOUND)
                    ->setContent([
                        Response::_ERROR_CODE => Response::HTTP_NOT_FOUND,
                        Response::_ERROR_MSG => 'Not found'
                    ])
                    ->getHeaderManager()
                    ->add(Headers::CONTENT_TYPE, 'application/json; charset=utf-8')
                    ->addMany([
                        Headers::HEADER_ACA_ORIGIN  =>  '*',
                        Headers::HEADER_ACA_CREDENTIALS => 'true',
                        Headers::HEADER_ACA_METHODS => implode(',', [
                            Request::METHOD_GET, Request::METHOD_POST, Request::METHOD_PUT,
                            Request::METHOD_DELETE, Request::METHOD_OPTIONS
                        ]),
                        Headers::HEADER_ACA_HEADERS =>  implode(',', [
                            'Access-Control-Allow-Headers', 'Origin', 'Accept',
                            'X-Requested-With', 'Content-Type', 'Access-Control-Request-Method',
                            'Access-Control-Request-Headers', 'Access-Control-Allow-Origin',
                            'Authorization'
                        ])
                    ]);
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
        return (!$this->isExclude()
            && $this->requestUriPrefix() === $this->prefix);
    }

    /**
     * isExclude
     *
     * @return Boolean
     */
    protected function isExclude(): bool
    {
        $disallowed = $this->configParams[get_called_class()]['exclude'];
        for ($c = 0; $c < count($disallowed); ++$c) {
            $composed = $this->prefix . $disallowed[$c];
            $isAuth = ($composed == $this->request->getUri());
            if ($isAuth) {
                return true;
            }
        }
        return false;
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
