<?php

namespace App\Component\Model\Orm;

use Nymfonya\Component\Container;

interface IOrm
{

    const SQL_ALL = '*';
    const SQL_WILD = '%';
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
    const OP_NOT = '!';
    const OP_LT = '<';
    const OP_GT = '>';
    const OP_LIKE = '#';
    const OPERATORS = [self::OP_NOT, self::OP_LT, self::OP_GT, self::OP_LIKE];

    public function __construct(Container $container);

    public function find(array $what = [], array $where = []);

    public function count(array $where = []);

    public function update(array $what = [], array $where = []);

    public function insert(array $what = []);

    public function delete(array $where = []);
}
