<?php

declare(strict_types=1);

namespace App\Reuse\Controllers\Api;

use App\Component\Cache\Redis\Adapter as RedisAdapter;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;

trait TRedisCache
{

    protected $cacheTtl = 60 * 5;
    protected $cacheFilename = '';

    /**
     * Redis client
     *
     * @var \Redis
     */
    protected $redisClient;

    /**
     * init redis client
     *
     * @param Container $container
     * @return void
     */
    protected function initRedisClient(Container $container): void
    {
        if (is_null($this->redisClient)) {
            $redisAdapter = new RedisAdapter(
                $container->getService(Config::class)
            );
            $this->redisClient = $redisAdapter->getClient();
        }
    }

    /**
     * return true if cache is expired
     *
     * @param string $key
     * @return boolean
     */
    protected function cacheRedisExpired(string $key = ''): bool
    {
        $cfn = (empty($key))
            ? $this->getRedisCacheFilename()
            : $key;
        $exist = $this->redisClient->exists($cfn);
        return !$exist;
    }

    /**
     * set cache ttl
     *
     * @param integer $ttl
     * @return void
     */
    protected function setRedisCacheTtl(int $ttl): void
    {
        $this->cacheTtl = $ttl;
    }

    /**
     * returns cache content
     *
     * @param string $key
     * @return mixed
     */
    protected function getRedisCache(string $key = '')
    {
        $cfn = (empty($key))
            ? $this->getRedisCacheFilename()
            : $key;
        $cache = $this->redisClient->get($cfn);
        return (\is_bool($cache))
            ? null
            : json_decode(
                $cache,
                true,
                512,
                JSON_OBJECT_AS_ARRAY
            );
    }

    /**
     * set cache content
     *
     * @param mixed $content
     * @param string $key
     * @return void
     */
    protected function setRedisCache($content, $key = ''): void
    {
        $cfn = (empty($key))
            ? $this->getRedisCacheFilename()
            : $key;
        $this->redisClient->set($cfn, json_encode($content));
        $this->redisClient->expire($cfn, $this->cacheTtl);
    }

    /**
     * clear cache entry
     *
     * @param string $key
     * @return void
     */
    protected function clearRedisCache(string $key = '')
    {
        $key = \strval($key);
        if (empty($key)) {
            $this->redisClient->del($this->getRedisCacheFilename());
        } elseif ($key == '*') {
            $this->redisClient->flushAll();
        } else {
            $this->redisClient->del($key);
        }
    }

    /**
     * returns cache filename from request uri
     *
     * @return string
     */
    protected function getRedisCacheFilename(): string
    {
        $path = $this->getRedisCachePath();
        $filename = md5($this->request->getUri());
        return $path . $filename;
    }

    /**
     * returns cache path from request script filename
     *
     * @return string
     */
    protected function getRedisCachePath(): string
    {
        return 'API_REQUEST_REDIS_CACHE_';
    }
}
