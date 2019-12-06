<?php

namespace App\Controllers\Api\V1;

use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Response;
use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\AbstractApi;
use App\Model\Repository\Metro\Lines;
use App\Model\Repository\Metro\Stations;

final class Metro extends AbstractApi implements IApi
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
     * @return Test
     */
    final public function lines(): Metro
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

    /**
     * upload with jwt bearer
     *
     * @return Test
     */
    final public function stations(): Metro
    {
        $appPath = dirname(dirname($this->request->getFilename()));
        $uploader = new Uploader();
        $uploader
            ->setTargetPath($appPath . '/assets/upload/')
            ->process();
        $resCodeError = $uploader->isError()
            ? Response::HTTP_INTERNAL_SERVER_ERROR
            : Response::HTTP_OK;
        $this->response->setCode($resCodeError)->setContent(
            $uploader->getInfos()
        );
        unset($uploader);
        return $this;
    }
}
