<?php

namespace App\Middlewares\Reuse;

use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Http\Kernel;
use Monolog\Logger;

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
     * exclude requirement
     *
     * @var array
     */
    protected $exclude;

    /**
     * uri prefix to match if middleware is required
     *
     * @var string
     */
    protected $prefix;

    /**
     * init minimal requirements settings
     *
     * @param array $container
     * @return void
     */
    protected function init(Container $container)
    {
        $this->kernel = $container->getService(
            Kernel::class
        );
        $this->config = $container->getService(
            Config::class
        );
        $this->configParams = $this->config->getSettings(
            Config::_MIDDLEWARES
        )[get_called_class()];
        $this->request = $container->getService(Request::class);
        $this->headers = $this->request->getHeaders();
        $this->response = $container->getService(Response::class);
        $this->logger = $container->getService(\Monolog\Logger::class);
        $this->enabled = $this->configParams['enabled'];
        $this->prefix = $this->configParams['prefix'];
        $this->exclude = $this->configParams['exclude'];
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
