<?php

namespace App\Controllers\Api\V1;

use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Container;
use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\AbstractApi;

final class Stat extends AbstractApi implements IApi
{

    const _SCRIPTS = 'scripts';

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
     * opcache
     *
     * @Role anonymous
     * @return Stat
     */
    final public function opcache(): Stat
    {
        $this->response
            ->setCode(Response::HTTP_SERVICE_UNAVAILABLE)
            ->setContent([
                Response::_ERROR => true,
                Response::_ERROR_MSG => 'Opcache disable'
            ]);
        $status = opcache_get_status();
        if (!empty($status)) {
            $path = dirname(dirname($this->request->getFilename()));
            $scripts = array_filter(
                $status[self::_SCRIPTS],
                function($val) use ($path) {
                    return false !== strpos($val['full_path'], $path);
                }
            );
            $status[self::_SCRIPTS] = array_values($scripts);
            $bytes = array_reduce(
                $status[self::_SCRIPTS],
                function($stack, $val) {
                    return $stack + (int) $val['memory_consumption'];
                }
            );
            $scriptCount = count($scripts);
            unset($scripts);
            $this->response
                ->setCode(Response::HTTP_OK)
                ->setContent(
                    [
                        'error' => false,
                        'datas' => [
                            'php_version' => phpversion(),
                            'nb_files' => $scriptCount,
                            'memory_used' => $bytes,
                            'status' => $status
                        ]
                    ]
                );
            unset($scriptCount, $scripts, $bytes);
        }
        unset($status);
        return $this;
    }

    /**
     * filecache
     *
     * @Role anonymous
     * @return Stat
     */
    final public function filecache(): Stat
    {
        $path = realpath($this->getCachePath());
        $files = glob($path . '/*');
        $this->response
            ->setCode(Response::HTTP_OK)
            ->setContent(
                [
                    'error' => false,
                    'datas' => [
                        'cache_path' => $path,
                        'cache_hits' => count($files),
                        'cache_files' => $files,
                    ]
                ]
            );
        unset($path, $files);
        return $this;
    }
}
