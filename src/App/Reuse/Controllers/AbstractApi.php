<?php

namespace App\Reuse\Controllers;

use App\Interfaces\Controllers\IApi;
use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Response;
use \Monolog\Logger;

abstract class AbstractApi implements IApi
{

    use \App\Reuse\Controllers\Api\TFileCache;

    /**
     * request
     *
     * @var Request
     */
    protected $request;

    /**
     * response
     *
     * @var Response
     */
    protected $response;

    /**
     * logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * di container
     *
     * @var Container
     */
    private $container;

    /**
     * instanciate
     *
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->request = $this->getService(Request::class);
        $this->response = $this->getService(Response::class);
        $this->logger = $this->getService(Logger::class);
    }

    /**
     * preflight CORS with OPTIONS method
     *
     * @Methods OPTIONS
     * @return IApi
     */
    public function preflight(): IApi
    {
        $this->response->setCode(200)->setContent([]);
        return $this;
    }

    /**
     * returns container service from a service name
     *
     * @param string $serviceName
     * @return object
     */
    protected function getService(string $serviceName)
    {
        return $this->container->getService($serviceName);
    }

    /**
     * return container instance
     *
     * @return Container
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * get request instance
     *
     * @return Request
     */
    protected function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * get request params
     *
     * @return array
     */
    protected function getParams(): array
    {
        return $this->getRequest()->getParams();
    }
}
