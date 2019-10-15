<?php

namespace App\Controllers\Api\V1;

use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\Api;
use App\Http\Request;
use App\Http\Response;
use App\Container;

final class Stat extends Api implements IApi
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
     * stat
     *
     * @Role anonymous
     * @return Stat
     */
    final public function cache(): Stat
    {
        $status = opcache_get_status();
        $this->response->setCode(Response::HTTP_OK)->setContent([
            Response::_ERROR => true,
            Response::_ERROR_MSG => 'Opcache disable'
        ]);
        if ($status) {
            $path = dirname(dirname($this->request->getFilename()));
            $scripts = array_filter($status['scripts'], function ($val) use ($path) {
                return strpos($val['full_path'], $path) !== false;
                return true;
            });
            $status['scripts'] = array_values($scripts);
            $bytes = array_reduce($status['scripts'], function ($stack, $val) {
                return $stack + $val['memory_consumption'];
            });
            $scriptCount = count($scripts);
            unset($scripts);
            $this->response
                ->setCode(Response::HTTP_OK)
                ->setContent(
                    [
                        'error' => false,
                        'datas' => [
                            'nb_files' => $scriptCount,
                            'memory_used' => $bytes,
                            'status' => $status
                        ]
                    ]
                );
            unset($scriptCount);
            unset($scripts);
        }
        unset($status);
        return $this;
    }
}
