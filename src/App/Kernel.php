<?php

namespace App;

use App\Http\Response;
use App\Http\Headers;

class Kernel
{

    const _PREFLIGHT = 'preflight';
    const PATH_CONFIG = '/../config/';

    use \App\Reuse\TKernel;

    /**
     * app instance
     *
     * @var Kernel
     */
    private static $instance;

    /**
     * instanciate
     *
     */
    public function __construct(string $env, string $path)
    {
        $this->init($env, $path);
        self::$instance = $this;
    }

    /**
     * destroy
     *
     */
    public function __destruct()
    {
        $this->req = null;
        $this->res = null;
        $this->config = null;
        $this->logger = null;
        $this->router = null;
        $this->reflector = null;
        $this->controller = null;
        $this->actions = [];
        $this->action = '';
        $this->container = null;
    }

    /**
     * set controller namespace
     *
     * @param string $ctrlNamespace
     * @return Kernel
     */
    public function setNameSpace(string $ctrlNamespace): Kernel
    {
        $this->nameSpace = $ctrlNamespace;
        return $this;
    }

    /**
     * run app
     *
     * @param array $routerGroups
     * @return Kernel
     */
    public function run(array $groups = []): Kernel
    {
        $routerGroups = ($groups)
            ? $groups
            : $this->router->compile();
        if ($routerGroups) {
            $this->setClassname($routerGroups);
            if (class_exists($this->className)) {
                $this->controller = new $this->className($this->container);
                $this->setReflector();
                $this->setActions();
                $this->setAction($routerGroups);
                //->setActionAnnotations();
                $this->setMiddleware();
            } else {
                $this->error = true;
                $this->errorCode = Response::HTTP_SERVICE_UNAVAILABLE;
            }
        }
        return $this;
    }

    /**
     * dispatch response
     *
     * @return Kernel
     */
    public function send(): Kernel
    {
        $logger = $this->getService(\Monolog\Logger::class);
        if ($this->getError()) {
            $this->res
                ->setCode($this->errorCode)
                ->setContent([
                    Response::_ERROR => $this->error,
                    Response::_ERROR_CODE => $this->errorCode,
                    Response::_ERROR_MSG => $this->errorMsg,
                ])
                ->getHeaderManager()
                ->add(
                    Headers::CONTENT_TYPE,
                    'application/json; charset=utf-8'
                );
            $logger->warning($this->errorMsg);
        } else {
            $logger->debug('Response sent');
        }
        $this->getService(\App\Http\Response::class)->send();
        return $this;
    }
}
