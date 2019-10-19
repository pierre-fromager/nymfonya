<?php

namespace App\Model;

use App\Container;
use App\Config;
use App\Model\AbstractSearch;
use App\Tools\Crypt;

/**
 * Class App\Model\Accounts
 *
 * Provides account list.
 */
class Accounts extends AbstractSearch
{
    const _ID = 'id';
    const _NAME = 'name';
    const _EMAIL = 'email';
    const _PASSWORD = 'password';
    const _STATUS = 'status';
    const _ROLE = 'role';
    const PATH_ASSETS_MODEL = '/../assets/model/';
    const ACCOUNTS_FILENAME = '/accounts.csv';
    const FIELD_SEPARATOR = ',';
    const FILTER_ALL = '/^(.*),(.*),(.*),(.*),(.*)/';

    /**
     * config
     *
     * @var Config
     */
    protected $config;

    /**
     * instanciate
     *
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->config = $this->getService(\App\Config::class);
        $this->init();
        return $this;
    }

    /**
     * init
     *
     * @param string $assetsPath
     * @return AbstractSearch
     */
    protected function init(): AbstractSearch
    {
        $this->setFilename($this->getAccountsFilename());
        $this->createFile();
        $this->setFilter(self::FILTER_ALL);
        $this->setSeparator(self::FIELD_SEPARATOR);
        return $this;
    }

    /**
     * return csv file accounts filename
     *
     * @return string
     */
    protected function getAccountsFilename(): string
    {
        return realpath(
            $this->config->getPath() . self::PATH_ASSETS_MODEL
        )  . self::ACCOUNTS_FILENAME;
    }

    /**
     * add account item to stack
     *
     * @param array $data
     * @return AbstractSearch
     */
    protected function setItem(array $data): AbstractSearch
    {
        $this->datas[] = [
            self::_ID => $data[0],
            self::_NAME => $data[1],
            self::_EMAIL => $data[2],
            self::_PASSWORD => $data[3],
            self::_STATUS => $data[4],
            self::_ROLE => $data[5]
        ];
        return $this;
    }

    /**
     * create csv file account from config accounts setting
     *
     * @return AbstractSearch
     */
    protected function createFile(): AbstractSearch
    {
        if (!file_exists($this->filename)) {
            $crypt = new Crypt($this->config);
            $accounts = $this->config->getSettings(Config::_ACCOUNTS);
            $accounts = array_map(function ($ac) use ($crypt) {
                $ac[self::_PASSWORD] = $crypt->encrypt(
                    $ac[self::_PASSWORD],
                    true
                );
                return $ac;
            }, $accounts);
            $fp = fopen($this->filename, 'w');
            foreach ($accounts as $record) {
                fputcsv($fp, array_values($record));
            }
            fclose($fp);
            unset($fp, $accounts, $crypt);
        }
        return $this;
    }
}
