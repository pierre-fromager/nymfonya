<?php

namespace App;

use App\Interfaces\IConfig;

/**
 * App\Config
 *
 * is a config manager
 *
 * @author pierrefromager
 */

class Config implements IConfig
{

    protected $path;
    protected $env;
    protected $settings;
    protected static $instance;

    /**
     * instanciate
     *
     * @param string $env
     */
    public function __construct(string $env, string $path)
    {
        $this->path = $path;
        $this->setEnv($env);
        $this->load();
        return $this;
    }

    /**
     * set config environment
     *
     * @param string $env
     * @return Config
     */
    public function setEnv(string $env = self::ENV_DEV): Config
    {
        $this->env = $env;
        return $this;
    }

    /**
     * set config path
     *
     * @param string $path
     * @return Config
     */
    public function setPath(string $path): Config
    {
        $this->path = $path;
        return $this;
    }

    /**
     * returns config path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * return config array for a main entry key
     *
     * @param string $key
     * @return array
     */
    public function getSettings(string $key = ''): array
    {
        return ($key) ? $this->settings[$key] : $this->settings;
    }

    /**
     * return true if config main entry for a key exists
     *
     * @param string $key
     * @return boolean
     */
    public function hasEntry(string $key): bool
    {
        return isset($this->settings[$key]);
    }

    /**
     * load config for a given env
     *
     * @return Config
     */
    public function load(): Config
    {
        $filename = realpath($this->getFilename());
        if (false === $this->check($filename)) {
            throw new \Exception(
                sprintf(
                    self::CONFIG_ERROR_MISSING . '%s on %s',
                    $this->env,
                    $this->path
                )
            );
        }
        $this->settings = require $this->getFilename();
        return $this;
    }

    /**
     * getFilename
     *
     * @return string
     */
    protected function getFilename(): string
    {
        return $this->path . $this->env . '.php';
    }

    /**
     * check
     *
     * @param string $filename
     * @return boolean
     */
    protected function check($filename): bool
    {
        return (in_array($this->env, $this->getAllowedEnv())
            && file_exists($filename));
    }

    /**
     * getAllowedEnv
     *
     * @return array
     */
    protected function getAllowedEnv(): array
    {
        return [
            self::ENV_DEV, self::ENV_INT, self::ENV_PROD,
            self::ENV_TEST, self::ENV_CLI
        ];
    }
}
