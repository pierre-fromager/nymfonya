<?php

namespace App\Component\Db\Adapter;

use \PDO;

class PdoMysql
{

    private $host;
    private $dbname;
    private $username;
    private $password;
    private $options;
    private $connection;

    /**
     * instanciate
     *
     * @param string $dbname
     * @param array $params
     */
    public function __construct(string $dbname, array $params)
    {
        $this->dbname = $dbname;
        $this->host = $params['host'];
        $this->username = $params['username'];
        $this->password = $params['password'];
        $this->options = $params['options'];
    }

     /**
      * connect to db
      *
      * @return void
      */
    public function connect()
    {
        $this->connection = new PDO(
            $this->dsn(),
            $this->username,
            $this->password,
            $this->options
        );
    }

    /**
     * return PDO instance
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * return dsn
     *
     * @return string
     */
    protected function dsn(): string
    {
        return sprintf(
            'mysql:host=%s;dbname=%s',
            $this->host,
            $this->dbname
        );
    }
}
