<?php

namespace App\Component\Migration;

use App\Component\Model\Orm\Orm;
use App\Component\Db\Migration;
use App\Model\Accounts;
use App\Model\Repository\Users as UsersRepository;
use Nymfonya\Component\Container;

class Users extends Migration
{

    /**
     * repository
     *
     * @var Orm
     */
    protected $repository;

    /**
     * Undocumented function
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->repository = new UsersRepository($container);
        $this->fromOrm($this->repository);
        $this->migrate();
    }

    /**
     * check if migration can run
     *
     * @return boolean
     */
    protected function canMigrate(): bool
    {
        $sql = 'SHOW TABLES LIKE ' . $this->repository->getTable();
        $this->run($sql)->hydrate();
        $result = $this->getRowset();
        return (count($result) == 0);
    }

    /**
     * sql to create table
     *
     * @return Migration
     */
    protected function runCreate(): Migration
    {
        $sql = "CREATE TABLE `users` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `password` varchar(255) NOT NULL,
            `status` varchar(255) NOT NULL,
            `role` varchar(255) NOT NULL,
            UNIQUE KEY `id` (`id`),
            KEY `email` (`email`)
          ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;";
        $this->run($sql);
        return $this;
    }

    /**
     * sql to insert into table
     *
     * @return Migration
     */
    protected function runInsert(): Migration
    {
        if (false === $this->isError()) {
            $csvArray = (new Accounts($this->container))->toArray();
            $fields = [
                'id', 'name', 'email', 'password', 'status', 'role'
            ];
            $insertDatas = array_map(function ($values) use ($fields) {
                return array_combine($fields, $values);
            }, $csvArray);
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
}
