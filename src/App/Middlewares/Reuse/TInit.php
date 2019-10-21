<?php

namespace App\Middlewares\Reuse;

use App\Kernel;
use App\Config;
use App\Http\Request;
use App\Http\Response;
use App\Container;
use \Monolog\Logger;

trait TInit
{

    /**
     * kernel
     *
     * @var Kernel
     */
    protected $kernel;

    /**
     * config
     *
     * @var Config
     */
    protected $config;

    /**
     * config middlewares params
     *
     * @var array
     */
    protected $configParams;

    /**
     * request
     *
     * @var Request
     */
    protected $request;

    /**
     * request headers
     *
     * @var array
     */
    protected $headers;

    /**
     * request
     *
     * @var Response
     */
    protected $response;

    /**
     * logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * enabled set from middlewares params
     *
     * @var Boolean
     */
    protected $enabled;

    /**
     * uri prefix to match if middleware is required
     *
     * @var string
     */
    private $prefix;

    /**
     * init minimal requirements settings
     *
     * @param array $container
     * @return void
     */
    protected function init(Container $container)
    {
        $this->config = $container->getService(\App\Config::class);
        $this->configParams =  $this->config->getSettings(
            Config::_MIDDLEWARES
        )[get_called_class()];
        $this->request = $container->getService(\App\Http\Request::class);
        $this->headers = $this->request->getHeaders();
        $this->response = $container->getService(\App\Http\Response::class);
        $this->logger = $container->getService(\Monolog\Logger::class);
        $this->enabled = $this->configParams['enabled'];
        $this->prefix = $this->configParams['prefix'];
    }

    /**
     * set enabled from $enable
     *
     * @param boolean $enable
     * @return void
     */
    protected function setEnabled(bool $enable)
    {
        $this->enabled = $enable;
    }
}
