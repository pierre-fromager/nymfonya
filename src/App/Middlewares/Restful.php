<?php

/**
 * App1\Middleware\Restfull
 *
 * is a request url rewriter to match action controller from request method
 * according to restfull base requirements
 */

namespace App1\Middleware;

use Pimvc\Http\Request as PimRequest;

class Restful implements \Pimvc\Http\Interfaces\Layer
{
    const RESTUL_ERROR = 'error';
    const RESTUL_ERROR_MESSAGE = 'errorMessage';
    const RESTUL_ERROR_NOROUTE_MESSAGE = 'No matching route';
    const RESTFULL_DEBUG = true;
    const RESTFULL_DEFAULT_ACTION = 'index';
    const RESTFULL_URI_PREFIX = '/api/v1/';
    const RESTFULL_URI_SUFFIX = '/context/json';
    const RESTFULL_ACTION_INDEX = 'index';
    const RESTFULL_ACTION_STORE = 'store';
    const RESTFULL_ACTION_PUT = 'update';
    const RESTFULL_ACTION_DELETE = 'destroy';
    const RESTFULL_ACTION_OPTIONS = 'preflight';
    const RESTFULL_METHODS_ACTIONS = [
        PimRequest::REQUEST_METHOD_GET => self::RESTFULL_ACTION_INDEX,
        PimRequest::REQUEST_METHOD_POST => self::RESTFULL_ACTION_STORE,
        PimRequest::REQUEST_METHOD_PUT => self::RESTFULL_ACTION_PUT,
        PimRequest::REQUEST_METHOD_DELETE => self::RESTFULL_ACTION_DELETE,
        PimRequest::REQUEST_METHOD_OPTIONS => self::RESTFULL_ACTION_OPTIONS
    ];

    private $method;
    private $app;
    private $controller;
    private $action;
    private $qparams;

    /**
     * peel
     *
     * @param type $object
     * @param \Closure $next
     * @return type
     */
    public function peel($object, \Closure $next)
    {
        $this->process();
        return $next($object);
    }

    /**
     * process
     *
     */
    private function process()
    {
        $this->app = \Pimvc\App::getInstance();
        $this->method = $this->app->getRequest()->getMethod();
        if ($this->isValid()) {
            $this->setCar();
            $this->rewriteUri();
        }
    }

    /**
     * isValid
     *
     * @return boolean
     */
    private function isValid()
    {
        return ($this->isValidMethod() && $this->required());
    }

    /**
     * isValidMethod
     *
     * @return boolean
     */
    private function isValidMethod()
    {
        $methods = self::RESTFULL_METHODS_ACTIONS;
        return isset($methods[$this->method]);
    }

    /**
     * required
     *
     * @return boolean
     */
    private function required()
    {
        return (bool) ($this->uriPrefix() === self::RESTFULL_URI_PREFIX);
    }

    /**
     * uriPrefix
     *
     * @return string
     */
    private function uriPrefix()
    {
        return substr(
            $this->app->getRequest()->getUri(),
            0,
            strlen(self::RESTFULL_URI_PREFIX)
        );
    }

    /**
     * setCar
     */
    private function setCar()
    {
        $prerouting = $this->app->getRouter()->compile();
        if ($prerouting === null) {
            $this->dispatchError(404, self::RESTUL_ERROR_NOROUTE_MESSAGE);
        }
        $prerouting[1] = self::RESTFULL_DEFAULT_ACTION;
        if (!isset($prerouting[2])) {
            $prerouting[2] = '';
        }
        list($this->controller, $this->action, $this->qparams) = $prerouting;
        $this->log();
        unset($prerouting);
    }

    /**
     * dispatchError
     *
     * @param int $httpCode
     * @param string $errorMessage
     */
    private function dispatchError($httpCode, $errorMessage)
    {
        $resp = $this->app->getResponse();
        $resp->setType($resp::TYPE_JSON);
        $resp->setHttpCode($httpCode);
        $resp->setContent([
            self::RESTUL_ERROR => true,
            self::RESTUL_ERROR_MESSAGE => $errorMessage
        ]);
        $resp->dispatch($andDie = true);
    }

    /**
     * rewriteUri
     *
     */
    private function rewriteUri()
    {
        $this->action = self::RESTFULL_METHODS_ACTIONS[$this->method];
        $rewritedUri = $this->controller . '/' . $this->action;
        if ($this->qparams) {
            $rewritedUri .= $this->qparams;
        }
        $this->qparams .= self::RESTFULL_URI_SUFFIX;
        $this->app->getRouter()->setUri($rewritedUri);
    }

    /**
     * log
     *
     */
    private function log()
    {
        if (self::RESTFULL_DEBUG) {
            $this->app->getLogger()->log(
                __CLASS__,
                \Pimvc\Logger::DEBUG,
                [
                    'controller' => $this->controller,
                    'action' => $this->action,
                    'params' => $this->qparams,
                ]
            );
        }
    }
}
