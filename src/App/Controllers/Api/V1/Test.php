<?php

namespace App\Controllers\Api\V1;

use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\AbstractApi;
use App\Http\Response;
use App\Container;

final class Test extends AbstractApi implements IApi
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
     * jwtaction
     *
     * @Role anonymous
     * @return Test
     */
    final public function jwtaction(): Test
    {
        $this->response->setCode(Response::HTTP_OK)->setContent([
            Response::_ERROR => false,
            Response::_ERROR_MSG => 'Jwt auth succeeded',
            'datas' => [
                'user' => $this->request->getSession('auth', 'user')
            ]
        ]);
        return $this;
    }
}
