<?php

namespace App\Controllers\Api\V1;

use App\Interfaces\Controllers\IApi;
use App\Interfaces\Controllers\IRestful;
use App\Reuse\Controllers\AbstractApi;
use App\Component\Http\Response;
use App\Component\Container;
use App\Model\Repository\Users;
use App\Component\Db\Core;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Api Restful Controller"
 * )
 */
final class Restful extends AbstractApi implements IApi, IRestful
{

    /**
     * core db instance
     *
     * @var Core
     */
    protected $db;

    /**
     * user repository
     *
     * @var Users
     */
    protected $userRepository;

    /**
     * slugs
     *
     * @var array
     */
    protected $slugs;

    /**
     * sql
     *
     * @var String
     */
    protected $sql;

    /**
     * sql values to bind statement
     *
     * @var array
     */
    protected $bindValues;

    /**
     * error
     *
     * @var Boolean
     */
    protected $error;

    /**
     * error message
     *
     * @var String
     */
    protected $errorMessage;

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->userRepository = new Users($container);
        $this->db = new Core($container);
        $this->db->fromOrm($this->userRepository);
        $this->error = false;
        $this->errorMessage = '';
        parent::__construct($container);
    }

    /**
     * index
     *
     * @OA\Get(
     *     path="/api/v1/restful/index",
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
    final public function index(array $slugs = []): Restful
    {
        $this->slugs = $slugs;
        $this->userRepository->find(
            ['name'],
            [
                'name' => ['john', 'elisa'],
                'jobs>' => 1
            ]
        );
        $this->sql = $this->userRepository->getSql();
        $this->bindValues = $this->userRepository->getBuilderValues();
        $this->db->run($this->sql, $this->bindValues)->hydrate();
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
    final public function store(array $slugs = []): Restful
    {
        $this->slugs = $slugs;
        $this->bindValues = [];
        try {
            $this->userRepository->insert($this->getParams());
            $this->sql = $this->userRepository->getSql();
            $this->bindValues = $this->userRepository->getBuilderValues();
        } catch (\Exception $e) {
            $this->error = true;
            $this->errorMessage = $e->getMessage();
            $this->sql = '';
        }
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
    final public function update(array $slugs = []): Restful
    {
        $this->slugs = $slugs;
        $this->bindValues = [];
        try {
            $params = $this->getParams();
            $pk = $this->userRepository->getPrimary();
            if (false === isset($params[$pk])) {
                throw new \Exception('Missing primary : ' . $pk);
            }
            $pkValue = $params[$pk];
            unset($params[$pk]);
            $this->userRepository->update($params, [$pk => $pkValue]);
            $this->sql = $this->userRepository->getSql();
            $this->bindValues = $this->userRepository->getBuilderValues();
        } catch (\Exception $e) {
            $this->error = true;
            $this->errorMessage = $e->getMessage();
            $this->sql = '';
        }
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
    final public function delete(array $slugs = []): Restful
    {
        $this->slugs = $slugs;
        $this->bindValues = [];
        try {
            $params = $this->getParams();
            $pk = $this->userRepository->getPrimary();
            if (false === isset($params[$pk])) {
                throw new \Exception('Missing primary : ' . $pk);
            }
            $this->userRepository->delete([$pk => $params[$pk]]);
            $this->sql = $this->userRepository->getSql();
            $this->bindValues = $this->userRepository->getBuilderValues();
        } catch (\Exception $e) {
            $this->error = true;
            $this->errorMessage = $e->getMessage();
            $this->sql = '';
        }
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
            ->setCode($this->getStatusCode())
            ->setContent(
                [
                    'error' => $this->error,
                    'errorMessage' => $this->errorMessage,
                    'datas' => [
                        'method' => $this->getRequest()->getMethod(),
                        'params' => $this->getParams(),
                        'controller' => $classname,
                        'action' => $action,
                        'query' => $this->sql,
                        'queryValues' => $this->bindValues,
                        'slugs' => $this->slugs,
                        'rowset' => $this->db->getRowset()
                    ]
                ]
            );
        return $this;
    }

    /**
     * returns true if error happened
     *
     * @return boolean
     */
    protected function isError(): bool
    {
        return $this->error === true;
    }

    /**
     * returns http status code
     *
     * @return int
     */
    protected function getStatusCode(): int
    {
        return (true === $this->isError())
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_OK;
    }
}
