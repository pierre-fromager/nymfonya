<?php

namespace App\Component\Db\Adapter;

use \PDO;

class PdoPgsql
{

    /**
     * hostname ip
     *
     * @var String
     */
    private $host;

    /**
     * port number
     *
     * @var integer
     */
    private $port;

    /**
     * database name
     *
     * @var String
     */
    private $dbname;

    /**
     * login name
     *
     * @var String
     */
    private $username;

    /**
     * password
     *
     * @var String
     */

    private $password;

    /**
     * options
     *
     * @var array
     */
    private $options;

    /**
     * connexion
     *
     * @var PDO
     */
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
        $this->port = $params['port'];
        $this->username = $params['username'];
        $this->password = $params['password'];
        $this->options = $params['options'];
    }

    /**
     * connect to db
     *
     * @return PdoPgsql
     */
    public function connect(): PdoPgsql
    {
        $this->connection = new PDO(
            $this->dsn(),
            $this->username,
            $this->password,
            $this->options
        );
        return $this;
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
     * dsn
     *
     * @return string
     */
    protected function dsn(): string
    {
        return sprintf(
            'pgsql:host=%s;dbname=%s;port=%s',
            $this->host,
            $this->dbname,
            $this->port
        );
    }
}
