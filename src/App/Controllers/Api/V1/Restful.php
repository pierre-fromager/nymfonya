<?php

namespace App\Controllers\Api\V1;

use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\AbstractApi;
use App\Http\Response;
use App\Container;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Api Restful Controller"
 * )
 */
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
     * @OA\Get(
     *     path="/api/v1/restful",
     *     summary="Search for a something item",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 example={"id": 10}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     * @return Restful
     */
    final public function index(): Restful
    {
        return $this->setResponse(__CLASS__, __FUNCTION__);
    }

    /**
     * store
     *
     * @OA\Post(
     *     path="/api/v1/restful",
     *     summary="Adds a new something item",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 example={"id": 10, "name": "Jessica Smith"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     * @return Restful
     */
    final public function store(): Restful
    {
        return $this->setResponse(__CLASS__, __FUNCTION__);
    }

    /**
     * update
     *
     * @OA\Put(
     *     path="/api/v1/restful",
     *     summary="Modify something item",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 example={"id": 10, "name": "Jessica Smoke"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     * @return Restful
     */
    final public function update(): Restful
    {
        return $this->setResponse(__CLASS__, __FUNCTION__);
    }

    /**
     * delete
     *
     * @OA\Delete(
     *     path="/api/v1/restful",
     *     summary="Delete something item",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="string"
     *                 ),
     *                 example={"id": 10}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
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
