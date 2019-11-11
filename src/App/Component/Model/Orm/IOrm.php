<?php

namespace App\Component\Model\Orm;

use App\Container;

interface IOrm
{

    const SQL_STATEMENTS_SELECT = 'select';
    const SQL_STATEMENTS_UPDATE = 'update';
    const SQL_STATEMENTS_INSERT = 'insert';
    const SQL_STATEMENTS_DELETE = 'delete';
    const SQL_STATEMENTS = [
        self::SQL_STATEMENTS_SELECT,
        self::SQL_STATEMENTS_UPDATE,
        self::SQL_STATEMENTS_INSERT,
        self::SQL_STATEMENTS_DELETE,
    ];

    public function __construct(Container $container);

    public function find(array $what = [], array $where = []);
    
    public function count(array $where = []);

    public function update(array $what = [], array $where = []);

    public function insert(array $what = []);

    public function delete(array $where = []);
}
