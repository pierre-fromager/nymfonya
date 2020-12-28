<?php

declare(strict_types=1);

namespace App\Reuse\Controllers;

use Nymfonya\Component\Container;
use App\Reuse\Controllers\AbstractApi;
use App\Reuse\Controllers\Api\TRedisCache;
use App\Reuse\Controllers\Api\TFileCache;

abstract class Cacheable extends AbstractApi
{

    const CACHE_TTL = 3600;

    use TRedisCache;
    use TFileCache;

    /**
     * constructor
     *
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->initRedisClient($container);
        $this->setRedisCacheTtl(self::CACHE_TTL);
    }
}
