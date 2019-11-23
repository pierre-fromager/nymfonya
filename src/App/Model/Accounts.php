<?php

namespace App\Model;

use Nymfonya\Component\Container;
use Nymfonya\Component\Config;
use App\Component\Auth\AuthInterface;
use App\Model\AbstractSearch;
use App\Component\Crypt;

/**
 * Class App\Model\Accounts
 *
 * Provides account list from file accounts.csv.
 */
class Accounts extends AbstractSearch implements AuthInterface
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
    const FILTER_ALL = '/^(.*),(.*),(.*),(.*),(.*),(.*)/';

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
        $this->config = $this->getService(Config::class);
        $this->init();
        return $this;
    }

    /**
     * auth
     *
     * @return array
     */
    public function auth(string $login, string $password): array
    {
        $crypt = new Crypt($this->config);
        $filter =
            '/^(.*),'
            . '(.*),'
            . '(' . $login . '),'
            . '(.*),'
            . '(.*),'
            . '(.*)/';
        $this->setFilter($filter)->readFromStream();
        $result = $this->get();
        if (empty($result)) {
            return [];
        }
        $user = $result[0];
        if ($password == $crypt->decrypt($user[self::_PASSWORD])) {
            return $user;
        }
        unset($crypt);
        return [];
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
        $this->createFile($this->filename);
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
        ) . self::ACCOUNTS_FILENAME;
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
     * @param string $filename
     * @return AbstractSearch
     */
    protected function createFile(string $filename): AbstractSearch
    {
        if (!file_exists($filename)) {
            $crypt = new Crypt($this->config);
            $accounts = $this->config->getSettings(Config::_ACCOUNTS);
            $accounts = array_map(function ($acc) use ($crypt) {
                $acc[self::_PASSWORD] = $crypt->encrypt(
                    $acc[self::_PASSWORD],
                    true
                );
                return $acc;
            }, $accounts);
            $handler = fopen($filename, 'w');
            $error = (false === $handler);
            if ($error === false) {
                foreach ($accounts as $record) {
                    fputcsv($handler, array_values($record));
                }
                fclose($handler);
            }
            unset($handler, $accounts, $crypt);
        }
        return $this;
    }
}
