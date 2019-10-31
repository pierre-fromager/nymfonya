<?php

namespace App\Controllers\Api\V1;

use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\AbstractApi;
use App\Http\Response;
use App\Container;

final class Restful extends AbstractApi implements IApi
{

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     * index
     *
     * @Request.method GET
     * @return Restful
     */
    final public function index(): Restful
    {
        return $this->setResponse(__CLASS__, __FUNCTION__);
    }

    /**
     * store
     *
     * @Request.method POST
     * @return Restful
     */
    final public function store(): Restful
    {
        return $this->setResponse(__CLASS__, __FUNCTION__);
    }

    /**
     * update
     *
     * @Request.method PUT/PATCH
     * @return Restful
     */
    final public function update(): Restful
    {
        return $this->setResponse(__CLASS__, __FUNCTION__);
    }

    /**
     * delete
     *
     * @Request.method DELETE
     * @return Restful
     */
    final public function delete(): Restful
    {
        return $this->setResponse(__CLASS__, __FUNCTION__);
    }

    /**
     * set response with for a classname and action
     *
     * @param string $classname
     * @param string $action
     * @return Restful
     */
    protected function setResponse(string $classname, string $action): Restful
    {
        $this->response
            ->setCode(Response::HTTP_OK)
            ->setContent(
                [
                    'error' => false,
                    'datas' => [
                        'method' => $this->request->getMethod(),
                        'params' => $this->request->getParams(),
                        'controller' => $classname,
                        'action' => $action,
                    ]
                ]
            );
        return $this;
    }
}
