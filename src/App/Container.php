<?php

namespace App;

use stdClass;

class Container
{

    const _CONSTRUCT = '__construct';

    /**
     * is the config array
     *
     * @var array
     */
    protected $servicesConfig;

    /**
     * service storage
     *
     * @var array
     */
    protected $services;

    /**
     * reporter
     *
     * @var stdClass
     */
    protected $reporter;

    /**
     * instanciate
     *
     * @param array $servicesConfig
     */
    public function __construct(array $servicesConfig)
    {
        $this->setServiceConfig($servicesConfig);
        $this->services = [];
        $this->initReporter();
        $this->load();
    }

    /**
     * init reporter
     *
     * @return void
     */
    protected function initReporter(): Container
    {
        $this->reporter = new \stdClass();
        $this->reporter->injected = 0;
        $this->reporter->constructable = 0;
        $this->reporter->unconstructable = 0;
        $this->reporter->exists = 0;
        $this->reporter->notexists = 0;
        return $this;
    }

    /**
     * returns reporter
     *
     * @return object
     */
    public function getReporter(): stdClass
    {
        return $this->reporter;
    }

    /**
     * return all services's container
     *
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * return service from classname
     *
     * @param string $serviceName
     * @return object
     * @throws Exception
     */
    public function getService(string $serviceName)
    {
        if (!$this->hasService($serviceName)) {
            throw new \Exception(
                sprintf('Container service %s undefined', $serviceName)
            );
        }
        return $this->services[$serviceName];
    }

    /**
     * load service from config service
     *
     * @return Container
     * @throws Exception
     */
    protected function load(): Container
    {
        if (count($this->servicesConfig) === 0) {
            throw new \Exception('Container config missing');
        }
        foreach ($this->servicesConfig as $serviceName => $serviceParams) {
            $this->create($serviceName, $serviceParams);
        }
        return $this;
    }

    /**
     * create a service and append in service containter
     *
     * @param string $serviceName
     * @param array $serviceParams
     * @return Container
     */
    protected function create(string $serviceName, array $serviceParams): Container
    {
        $this->createDependencies($serviceParams);
        $this->createCoreService($serviceName, $serviceParams);
        return $this;
    }

    /**
     * create core service
     *
     * @param string $serviceName
     * @param array $serviceParams
     * @return Container
     */
    protected function createCoreService(string $serviceName, array $serviceParams): Container
    {
        if ($this->constructable($serviceName)) {
            if (!$this->hasService($serviceName)) {
                $args = array_map(function ($v) {
                    if (is_array($v)) {
                        $sv = [];
                        foreach ($v as $i) {
                            $sv[] = ($this->constructable($i))
                                ? $this->services[$i]
                                : $i;
                        }
                        return $sv;
                    } else {
                        return ($this->constructable($v))
                            ? $this->services[$v]
                            : $v;
                    }
                }, $serviceParams);
                $this->injectService($serviceName, $args);
            }
        }
        return $this;
    }

    /**
     * create dependent services
     *
     * @param array $serviceParams
     * @return Container
     */
    protected function createDependencies(array $serviceParams): Container
    {
        foreach ($serviceParams as $serviceParam) {
            if (is_array($serviceParam)) {
                foreach ($serviceParam as $serviceParamsItem) {
                    $this->injectService($serviceParamsItem, []);
                }
            } else {
                $this->injectService($serviceParam, []);
            }
        }
        return $this;
    }

    /**
     * inject service in container
     *
     * @param string $serviceName
     * @param array $serviceParams
     * @return Container
     */
    protected function injectService($serviceName, array $serviceParams): Container
    {
        if ($this->constructable($serviceName)) {
            if (!$this->hasService($serviceName)) {
                $this->reporter->injected++;
                $this->services[$serviceName] = new $serviceName(...$serviceParams);
            }
        }
        return $this;
    }

    /**
     * return true is service class exists with a constructor
     *
     * @param mixed $serviceName
     * @return boolean
     */
    protected function constructable($value): bool
    {
        if ($this->isBasicType($value)) {
            return false;
        }
        $constructable = (class_exists($value)
            && is_callable([$value, self::_CONSTRUCT], true));
        if ($constructable) {
            ++$this->reporter->constructable;
        } else {
            ++$this->reporter->unconstructable;
        }
        return $constructable;
    }

    /**
     * return true if service instanciated in container
     *
     * @param string $serviceName
     * @return boolean
     */
    protected function hasService(string $serviceName): bool
    {
        $exists = isset($this->services[$serviceName]);
        if ($exists) {
            ++$this->reporter->exists;
        } else {
            ++$this->reporter->notexists;
        }
        return isset($this->services[$serviceName]);
    }

    /**
     * return true if is boolean or int types
     *
     * @param mixed $value
     * @return boolean
     */
    protected function isBasicType($value): bool
    {
        return (is_int($value) || is_bool($value));
    }

    /**
     * set config for service
     * testing purpose
     *
     * @param array $servicesConfig
     * @return Container
     */
    protected function setServiceConfig(array $servicesConfig): Container
    {
        $this->servicesConfig = $servicesConfig;
        return $this;
    }
}
