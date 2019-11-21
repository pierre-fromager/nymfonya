<?php

/**
 * App\Component\Interfaces\IConfig
 *
 * App\Component\Config interface
 *
 * @author pierrefromager
 */

namespace App\Component\Interfaces;

interface IConfig
{
    const CONFIG_REL_PATH = '../config/';
    const CONFIG_ERROR_MISSING = 'Missing config env for ';
    const ENV_DEV = 'dev';
    const ENV_TEST = 'test';
    const ENV_INT = 'int';
    const ENV_PROD = 'prod';
    const ENV_CLI = 'cli';
    const _MIDDLEWARES = 'middlewares';
    const _LOGGER = 'logger';
    const _NAME = 'name';
    const _PATH = 'path';
    const _ROUTES = 'routes';
    const _SERVICES = 'services';
    const _ACCOUNTS = 'accounts';
    const _DB = 'db';

    /**
     * set config environment
     *
     * @param string $env
     * @return IConfig
     */
    public function setEnv(string $env = self::ENV_DEV): IConfig;

    /**
     * returns env
     *
     * @return string
     */
    public function getEnv(): string;

    /**
     * set config path
     *
     * @param string $path
     * @return IConfig
     */
    public function setPath(string $path): IConfig;

    /**
     * returns config path
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * return config array for a main entry key
     *
     * @param string $key
     * @return array
     */
    public function getSettings(string $key = ''): array;

    /**
     * return true if config main entry for a key exists
     *
     * @param string $key
     * @return boolean
     */
    public function hasEntry(string $key): bool;

    /**
     * load config for a given env
     *
     * @return IConfig
     */
    public function load(): IConfig;
}
