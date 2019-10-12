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
        $this->servicesConfig = $servicesConfig;
        $this->services = [];
        $this->initReporter();
        $this->load();
    }

    private function initReporter()
    {
        $this->reporter = new \stdClass();
        $this->reporter->injected = 0;
        $this->reporter->constructable = 0;
        $this->reporter->unconstructable = 0;
        $this->reporter->exists = 0;
        $this->reporter->notexists = 0;
    }

    public function getReporter(): object
    {
        return $this->reporter;
    }

    /**
     * return all services's containter
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
     * Undocumented function
     *
     * @return void
     */
    protected function load()
    {
        if (!is_array($this->servicesConfig) && empty($this->servicesConfig)) {
            throw new \Exception('Container config missing');
        }
        foreach ($this->servicesConfig as $serviceName => $serviceParams) {
            $this->create($serviceName, $serviceParams);
        }
    }

    /**
     * create a service and append in service containter
     *
     * @param string $serviceName
     * @param array $serviceParams
     * @return void
     */
    protected function create(string $serviceName, array $serviceParams)
    {
        $this->createDependencies($serviceParams);
        $this->createCoreService($serviceName, $serviceParams);
    }

    /**
     * create core service
     *
     * @param string $serviceName
     * @param array $serviceParams
     * @return void
     */
    protected function createCoreService(string $serviceName, array $serviceParams)
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
    }

    /**
     * create dependent services
     *
     * @param array $serviceParams
     * @return void
     */
    protected function createDependencies(array $serviceParams)
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
    }

    /**
     * inject service in container
     *
     * @param string $serviceName
     * @param array $serviceParams
     * @return void
     */
    protected function injectService($serviceName, array $serviceParams)
    {
        if ($this->constructable($serviceName)) {
            if (!$this->hasService($serviceName)) {
                $this->reporter->injected++;
                $this->services[$serviceName] = new $serviceName(...$serviceParams);
            }
        }
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
}
