<?php

namespace App\Component\Db;

use App\Component\Model\Orm\Orm;
use App\Component\Db\Factory;
use Nymfonya\Component\Container;

class Core
{
    /**
     * connection
     *
     * @var \PDO | boolean
     */
    protected $connection;

    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * factory
     *
     * @var Factory
     */
    protected $factory;

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
     * @var \PDOStatement | boolean
     */
    protected $statement;

    /**
     * fetch mode
     *
     * @var int
     */
    protected $fetchMode = \PDO::FETCH_ASSOC;

    /**
     * rowset result
     *
     * @var array
     */
    protected $rowset;

    /**
     * error
     *
     * @var boolean
     */
    protected $error;

    /**
     * code error
     *
     * @var string
     */
    protected $errorCode;

    /**
     * error message
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * instanciate
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->factory = new Factory($this->container);
        $this->logger = $this->container->getService(\Monolog\Logger::class);
        $this->rowset = [];
        $this->resetError();
    }

    /**
     * set connection from Orm instance
     *
     * @param Orm $ormInstance
     * @return Core
     */
    public function fromOrm(Orm &$ormInstance): Core
    {
        $this->connection = $this->factory->getConnection(
            $ormInstance->getSlot(),
            $ormInstance->getDatabase()
        );
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
        $this->resetError();
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
        } catch (\PDOException $e) {
            $this->setError(true, $e->getCode(), $e->getMessage());
            $this->logger->alert('Core Db : Run failed');
            $this->logger->alert($this->errorMessage);
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
        if ($this->statement instanceof \PDOStatement) {
            $this->rowset = $this->statement->fetchAll($this->fetchMode);
            $this->statement->closeCursor();
        }
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
            } catch (\PDOException $e) {
                $this->setError(true, $e->getCode(), $e->getMessage());
                $this->logger->alert(
                    'Sql Bind Error [' . $key . ':' . $value . ':' . $type . ']'
                );
            }
        }
        return $this;
    }

    /**
     * set connection
     *
     * @param \PDO $connection
     * @return Core
     */
    protected function setConnection(\PDO $connection): Core
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * return true if error
     *
     * @return boolean
     */
    protected function isError(): bool
    {
        return $this->error === true;
    }

    /**
     * reset last error
     *
     * @return Core
     */
    protected function resetError(): Core
    {
        $this->setError(false, 0, '');
        return $this;
    }

    /**
     * get error code
     *
     * @return mixed
     */
    protected function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * get error message
     *
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * set error status, code and message
     *
     * @param boolean $status
     * @param integer | string $code
     * @param string $message
     * @return Core
     */
    protected function setError(bool $status, $code, string $message): Core
    {
        $this->error = $status;
        $this->errorCode = $code;
        $this->errorMessage = $message;
        return $this;
    }
}
