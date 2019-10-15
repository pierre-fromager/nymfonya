<?php

namespace App\Reuse;

use App\Kernel;
use App\Config;
use App\Container;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use Monolog\Logger;

trait TKernel
{

    /**
     * app path
     *
     * @var string
     */
    protected $path;

    /**
     * app environment
     *
     * @var string
     */
    protected $env;

    /**
     * app config
     *
     * @var Config
     */
    protected $config;

    /**
     * service container
     *
     * @var Container
     */
    protected $container;

    /**
     * monolog logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * app request
     *
     * @var Request
     */
    protected $req;

    /**
     * app response
     *
     * @var Response
     */
    protected $res;

    /**
     * app router
     *
     * @var Router
     */
    protected $router;

    /**
     * ctrl class name
     *
     * @var string
     */
    protected $className;

    /**
     * controller namespace
     *
     * @var string
     */
    protected $nameSpace;

    /**
     * controller instance
     *
     * @var mixed
     */
    protected $controller;

    /**
     * reflection class instance
     *
     * @var ReflectionClass
     */
    protected $reflector;

    /**
     * controller actions
     *
     * @var array
     */
    protected $actions;

    /**
     * controller action
     *
     * @var string
     */
    protected $action;

    /**
     * action annotations
     *
     * @var string
     */
    protected $actionAnnotations;

    /**
     * middlewares stack
     *
     * @var array
     */
    protected $middlewares;

    /**
     * run error
     *
     * @var Boolean
     */
    protected $error;

    /**
     * http status error code
     *
     * @var int
     */
    protected $errorCode;

    /**
     * error message
     *
     * @var string
     */
    protected $errorMsg;

    /**
     * return service container for service name
     *
     * @param string $serviceName
     * @throws Exception
     * @return object
     */
    public function getService(string $serviceName)
    {
        return $this->container->getService($serviceName);
    }

    /**
     * init kernel
     *
     * @param string $env
     * @param string $path
     * @return void
     */
    protected function init(string $env, string $path)
    {
        $this->env = $env;
        $this->path = $path;
        $this->error = true;
        $this->errorCode = Response::HTTP_NOT_FOUND;
        $this->errorMsg = 'Not Found';
        $this->setConfig();
        $this->setContainer();
        $this->setRequest();
        $this->setResponse();
        $this->setLogger();
        $this->setPath();
        $this->setRouter();
    }

    /**
     * execute controller action in middleware core closure
     *
     * @return void
     */
    protected function execute()
    {
        if ($this->isValidAction()) {
            $resExec = call_user_func_array(
                [$this->controller, $this->action],
                []
            );
            $this->error = ($resExec === false);
            $this->errorCode = ($this->error)
                ? Response::HTTP_INTERNAL_SERVER_ERROR
                : Response::HTTP_OK;
            $this->errorMsg = sprintf(
                'Execute %s',
                ($this->error) ? 'failed' : 'successfully'
            );
            $this->logger->debug($this->errorMsg);
        } else {
            $this->error = true;
            $this->errorMsg = 'Unkown endpoint';
            $this->errorCode = Response::HTTP_BAD_REQUEST;
        }
    }

    /**
     * set middlewares from config then run before/after layers around core
     *
     */
    protected function setMiddleware()
    {
        $middlwaresClasses = $this->config->getSettings(Config::_MIDDLEWARES);
        foreach ($middlwaresClasses as $name => $middleware) {
            $this->middlewares[$name] = new $middleware;
        }
        $this->middleware = new \App\Http\Middleware();
        $this->middleware->layer($this->middlewares)->peel(
            $this->container,
            function ($container) {
                $this->execute();
                return $container;
            }
        );
    }

    /**
     * set action annotations for runnig action
     *
     */
    protected function setActionAnnotations()
    {
        if ($this->isValidAction()) {
            $this->actionAnnotations = $this->reflector
                ->getMethod($this->action)
                ->getDocComment();
        }
    }

    /**
     * return true if action is valid
     *
     * @return boolean
     */
    protected function isValidAction(): bool
    {
        return in_array($this->action, $this->actions);
    }

    /**
     * set relflector on class name
     *
     */
    protected function setReflector()
    {
        $this->reflector = new \ReflectionClass($this->className);
    }

    /**
     * set controller action from router groups
     *
     * @param array $routerGroups
     */
    protected function setAction(array $routerGroups)
    {
        $req = $this->getService(\App\Http\Request::class);
        $isPreflight = ($req->getMethod() == Request::METHOD_OPTIONS);
        $action = isset($routerGroups[1]) ? strtolower($routerGroups[1]) : '';
        $this->action = ($isPreflight)
            ? Kernel::_PREFLIGHT
            : $action;
    }

    /**
     * set public final actions from controller class name
     *
     */
    protected function setActions()
    {
        $actions = array_map(function ($v) {
            return $v->name;
        }, $this->reflector->getMethods(\ReflectionMethod::IS_FINAL));
        $this->actions = array_merge($actions, [Kernel::_PREFLIGHT]);
    }

    /**
     * set service container
     *
     */
    protected function setContainer()
    {
        $this->container = new \App\Container(
            $this->config->getSettings(Config::_SERVICES)
        );
    }

    /**
     * get service container
     *
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * set app request
     *
     */
    protected function setRequest()
    {
        $this->req = $this->getService(\App\Http\Request::class);
    }

    /**
     * set app response
     *
     */
    protected function setResponse()
    {
        $this->res = $this->getService(\App\Http\Response::class);
    }

    /**
     * set app router
     *
     */
    protected function setRouter()
    {
        $this->router = $this->getService(\App\Http\Router::class);
    }

    /**
     * set controller class name
     *
     * @param array $routerGroups
     */
    protected function setClassname(array $routerGroups)
    {
        $this->className = $this->nameSpace
            . implode(
                '\\',
                array_map('ucfirst', explode('/', $routerGroups[0]))
            );
    }

    /**
     * set app config
     *
     */
    protected function setConfig()
    {
        $this->config = new Config(
            $this->env,
            $this->path . Kernel::PATH_CONFIG
        );
    }

    /**
     * set app logger
     *
     */
    protected function setLogger()
    {
        $this->logger = $this->getService(\Monolog\Logger::class);
        $handlers = $this->logger->getHandlers();
        foreach ($handlers as $handlder) {
            if ($handlder instanceof \Monolog\Handler\RotatingFileHandler) {
                $patchedHandlder = $handlder;
                $patchedHandlder->setFilenameFormat(
                    '{date}-{filename}',
                    'Y-m-d'
                );
                $this->logger->popHandler();
                $this->logger->pushHandler($patchedHandlder);
            }
        }
        unset($handlers);
        \Monolog\ErrorHandler::register($this->logger);
    }

    /**
     * set app root path
     *
     */
    protected function setPath()
    {
        $this->path = dirname($this->req->getFilename());
    }
}
