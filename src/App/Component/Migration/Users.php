<?php

declare(strict_types=1);

namespace App\Component\Migration;

use App\Component\Model\Orm\Orm;
use App\Component\Db\Migration;
use App\Model\Accounts;
use App\Model\Repository\Users as UsersRepository;
use Nymfonya\Component\Container;

class Users extends Migration
{
    const MIG_FIELDS = [
        'id', 'name', 'email', 'password', 'status', 'role'
    ];

    /**
     * repository
     *
     * @var Orm
     */
    protected $repository;

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->repository = new UsersRepository($container);
        $this->fromOrm($this->repository);
    }

    /**
     * check if migration can run
     *
     * @return boolean
     */
    protected function canMigrate(): bool
    {
        return (!$this->tableExists());
    }

    /**
     * create table
     *
     * @return Migration
     */
    protected function runCreate(): Migration
    {
        if (!$this->tableExists()) {
            $sql = sprintf(
                "CREATE TABLE `%s` (
                    `id` bigint(20) NOT NULL ,
                    `name` varchar(255) NOT NULL,
                    `email` varchar(255) NOT NULL,
                    `password` varchar(255) NOT NULL,
                    `status` varchar(255) NOT NULL,
                    `role` varchar(255) NOT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
                $this->repository->getTable()
            );
            $this->run($sql);
        }
        return $this;
    }

    /**
     * insert into table
     *
     * @return Migration
     */
    protected function runInsert(): Migration
    {
        if ($this->tableExists()) {
            $insertDatas = $this->getInsertDatas();
            foreach ($insertDatas as $data) {
                $this->repository->resetBuilder();
                $this->repository->insert($data);
                $this->run(
                    $this->repository->getSql(),
                    $this->repository->getBuilderValues()
                );
            }
        }
        return $this;
    }

    /**
     * index table
     *
     * @return Migration
     */
    protected function runIndex(): Migration
    {
        $pkey = $this->repository->getPrimary();
        $sqlIndex = sprintf(
            'ALTER TABLE `%s`'
                . 'ADD PRIMARY KEY (`%s`),'
                . 'ADD KEY `%s` (`%s`),'
                . 'ADD KEY `email` (`email`)',
            $this->repository->getTable(),
            $pkey,
            $pkey,
            $pkey
        );
        $this->run($sqlIndex);
        $sqlAutoinc = 'ALTER TABLE `users`'
            . 'MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1';
        $this->run($sqlAutoinc);
        return $this;
    }

    /**
     * prepare to insert datas as array
     *
     * @return array
     */
    protected function getInsertDatas(): array
    {
        $csvArray = (new Accounts($this->container))->toArray();
        $fields = self::MIG_FIELDS;
        $insertDatas = array_map(function ($values) use ($fields) {
            return array_combine($fields, $values);
        }, $csvArray);
        return $insertDatas;
    }

    /**
     * return true if table exists
     *
     * @return boolean
     */
    protected function tableExists(): bool
    {
        $sqlWhere = " WHERE table_schema = '%s' AND table_name = '%s';";
        $sql = sprintf(
            "SELECT count(*) as counter FROM %s " . $sqlWhere,
            'information_schema.tables',
            $this->repository->getDatabase(),
            $this->repository->getTable()
        );
        $this->run($sql)->hydrate();
        $result = $this->getRowset()[0];
        $counter = (int) $result['counter'];
        return ($counter > 0);
    }

    /**
     * drop table if exists
     *
     * @return boolean
     */
    protected function dropTable(): bool
    {
        if (!$this->tableExists()) {
            return false;
        }
        $sql = 'DROP TABLE ' . $this->repository->getTable() . ';';
        $this->run($sql);
        return true;
    }
}
