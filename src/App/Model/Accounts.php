<?php

namespace App\Model;

use App\Http\Request;
use App\Model\AbstractSearch;

/**
 * Class App\Model\Accounts
 *
 * Provides account list.
 */
class Accounts extends AbstractSearch
{
    const _CSV = 'csv';
    const _ID = 'id';
    const _NAME = 'name';
    const _EMAIL = 'email';
    const _PASSWORD = 'password';
    const ACCOUNTS_FILENAME = '/assets/accounts.' . self::_CSV;
    const FIELD_SEPARATOR = ";";

    /**
     * instanciate
     *
     */
    public function __construct(Request $req)
    {
        parent::__construct($req);
        $this->setFilename(
            $this->getAppPath($req) . self::ACCOUNTS_FILENAME
        );
        $this->separator = self::FIELD_SEPARATOR;
        return $this;
    }

    /**
     * add account item to stack
     *
     * @param array $data
     * @return Search
     */
    protected function setItem(array $data): AbstractSearch
    {
        $this->datas[] = [
            self::_ID => $data[0],
            self::_NAME => $data[1],
            self::_EMAIL => $data[2],
            self::_PASSWORD => $data[3],
        ];
        return $this;
    }

    /**
     * returns app path from request
     *
     * @param Request $req
     * @return string
     */
    private function getAppPath(Request $req): string
    {
        return dirname(dirname($req->getFilename()));
    }
}
