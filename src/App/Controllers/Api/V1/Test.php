<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Http\Request;
use App\Component\File\Uploader;
use App\Reuse\Controllers\Cacheable;

final class Test extends Cacheable
{
    use \App\Reuse\Controllers\Api\TRelay;

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

    /**
     * upload with jwt bearer
     *
     * @return Test
     */
    final public function upload(): Test
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

    /**
     * pokerelay
     *
     * @see https://pokeapi.co/
     * @return Test
     */
    final public function pokerelay(): Test
    {
        $pokeApiUrl = 'https://pokeapi.co/api/v2/pokemon/ditto/';
        $this->pokemonApiRelay($pokeApiUrl);
        return $this;
    }

    /**
     * run external api with cache and set response
     *
     * @param string $url
     * @return Test
     */
    protected function pokemonApiRelay(string $url): Test
    {
        if ($this->cacheFileExpired()) {
            $apiHeaders = [
                'Accept: application/json',
                //'Authorization: Bearer ' . $this->token
            ];
            $this->apiRelayRequest(Request::METHOD_GET, $url, $apiHeaders);
            if ($this->apiRelayHttpCode === Response::HTTP_OK) {
                $this->setFileCache($this->apiRelayResponse);
            }
        } else {
            $this->apiRelayResponse = $this->getFileCache();
            $this->apiRelayHttpCode = Response::HTTP_OK;
        }
        $statusCode = ($this->apiRelayHttpCode == Response::HTTP_OK)
            ? $this->apiRelayHttpCode
            : Response::HTTP_NOT_FOUND;
        $this->response->setCode($statusCode)->setContent($this->apiRelayResponse);
        return $this;
    }
}
