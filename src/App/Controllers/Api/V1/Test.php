<?php

namespace App\Controllers\Api\V1;

use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\AbstractApi;
use App\Http\Response;
use App\Http\Request;
use App\Container;

final class Test extends AbstractApi implements IApi
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
    protected function pokemonApiRelay(string $url):Test
    {
        if ($this->cacheExpired()) {
            $this->apiRelayRequest(
                Request::METHOD_GET,
                $url,
                [
                    'Accept: application/json',
                    //'Authorization: Bearer ' . $this->token
                ]
            );
            if ($this->apiRelayHttpCode === Response::HTTP_OK) {
                $this->setCache($this->apiRelayResponse);
            }
        } else {
            $this->apiRelayResponse = $this->getCache();
            $this->apiRelayHttpCode = Response::HTTP_OK;
        }
        $statusCode = ($this->apiRelayHttpCode == Response::HTTP_OK)
            ? $this->apiRelayHttpCode
            : Response::HTTP_NOT_FOUND;
        $this->response->setCode($statusCode)->setContent($this->apiRelayResponse);
        return $this;
    }
}
