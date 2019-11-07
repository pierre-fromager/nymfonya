<?php

namespace App\Component\Cache\Redis;

use App\Config;
use \Redis;

class Adapter
{
    const _REDIS = 'redis';
    const _HOST = 'host';
    const _PORT = 'port';

    /**
     * redis adapter config
     *
     * @var array
     */
    protected $config;

    /**
     * redis instance
     *
     * @var \Redis
     */
    protected $instance;

    /**
     * error
     *
     * @var Boolean
     */
    protected $error;

    /**
     * error code
     *
     * @var int
     */
    protected $errorCode;

    /**
     * error message
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * instanciate
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config->getSettings(self::_REDIS);
        $this->error = false;
        $this->errorCode = 0;
        $this->errorMessage = '';
    }

    /**
     * return redis client instance
     *
     * @return Redis
     */
    public function getClient(): Redis
    {
        if (is_null($this->instance)) {
            try {
                $this->instance =  new Redis();
                @$this->instance->connect(
                    $this->config[self::_HOST],
                    $this->config[self::_PORT]
                );
            } catch (\Exception $e) {
                $this->error = true;
                $this->errorCode = $e->getCode();
                $this->errorMessage = $e->getMessage();
            }
        }
        return $this->instance;
    }

    /**
     * return true if error
     *
     * @return boolean
     */
    public function isError(): bool
    {
        return $this->error === true;
    }

    /**
     * return code error
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * return error message
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
