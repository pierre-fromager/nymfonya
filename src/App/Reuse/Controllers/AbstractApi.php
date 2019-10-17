<?php

namespace App\Reuse\Controllers;

use App\Interfaces\Controllers\IApi;
use App\Container;
use App\Http\Request;
use App\Http\Response;

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
        $this->request = $this->getService(\App\Http\Request::class);
        $this->response = $this->getService(\App\Http\Response::class);
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
}
