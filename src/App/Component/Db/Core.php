<?php

namespace App\Component\Db;

use App\Component\Model\Orm\Orm;
use App\Component\Db\Factory;

class Core
{
    /**
     * connection
     *
     * @var \PDO | boolean
     */
    protected $connection;

    /**
     * logger
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * sql
     *
     * @var string
     */
    protected $sql;

    /**
     * statement
     *
     * @var \PDOStatement
     */
    protected $statement;

    /**
     * fetch mode
     *
     * @var int
     */
    protected $fetchMode = \PDO::FETCH_ASSOC;

    /**
     * database name
     *
     * @var string
     */
    protected $database;

    /**
     * rowset result
     *
     * @var array
     */
    protected $rowset;

    /**
     * set connection from Orm instance
     *
     * @param Orm $ormInstance
     * @return Core
     */
    public function fromOrm(Orm &$ormInstance): Core
    {
        $this->database = $ormInstance->getDatabase();
        $container = $ormInstance->getContainer();
        $this->logger = $container->getService(\Monolog\Logger::class);
        $factory = new Factory($container);
        $this->connection = $factory->getConnection(
            $ormInstance->getSlot(),
            $this->database
        );
        $this->rowset = [];
        return $this;
    }

    /**
     * run query with bind values and types
     *
     * @param string $sql
     * @param array $bindParams
     * @param array $bindTypes
     * @return Core
     */
    public function run(
        string $sql,
        array $bindParams = [],
        array $bindTypes = []
    ): Core {
        $this->sql = $sql;
        try {
            $this->statement = $this->connection->prepare($sql);
            if ($this->statement instanceof \PDOStatement) {
                $this->statement->setFetchMode($this->fetchMode);
                if (!empty($bindParams)) {
                    $this->bindArray(
                        $this->statement,
                        $bindParams,
                        $bindTypes
                    );
                }
            }
        } catch (\PDOException $exc) {
            $this->logger->alert('Prepare failed');
            $this->logger->alert($exc->getMessage());
        }
        try {
            $this->statement->execute();
        } catch (\PDOException $exc) {
            $this->logger->alert('Execute failed');
            $this->logger->alert($exc->getMessage());
        }
        return $this;
    }

    /**
     * hydrate
     *
     * @return Core
     */
    public function hydrate(): Core
    {
        $this->rowset = $this->statement->fetchAll($this->fetchMode);
        $this->statement->closeCursor();
        return $this;
    }

    /**
     * return run result
     *
     * @return array
     */
    public function getRowset(): array
    {
        return $this->rowset;
    }

    /**
     * bindArray
     *
     * @param \PDOStatement $poStatement
     * @param array $paArray
     * @param array $forcedTypes
     * @return $this
     */
    public function bindArray(\PDOStatement &$poStatement, array &$paArray, array $forcedTypes = [])
    {
        foreach ($paArray as $k => $v) {
            $type = (is_int($v)) ? \PDO::PARAM_INT : \PDO::PARAM_STR;

            if (isset($forcedTypes[$k])) {
                $type = $forcedTypes[$k];
                $v = ($type == \PDO::PARAM_INT) ? (int) $v : $v;
            }
            $value = is_array($v) ? serialize($v) : $v;
            $key =  $k;
            try {
                $poStatement->bindValue($key, $value, $type);
            } catch (\PDOException $exc) {
                $this->logger->alert(
                    'Sql Bind Error [' . $key . ':' . $value . ':' . $type . ']'
                );
            }
        }
        return $this;
    }
}
