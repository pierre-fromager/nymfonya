<?php

/**
 * slot => [ dbname => [...params]]
 */
return [
    'test' => [
        'nymfonya' => [
            'host' => 'localhost',
            'username' => 'travis_pass',
            'password' => 'some password',
            'adapter' => App\Component\Db\Adapter\PdoMysql::class,
            'options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                //, \PDO::ERRMODE_EXCEPTION => false
                \PDO::ATTR_CASE => \PDO::CASE_LOWER,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        ],
        'badnymfonya' => [
            'host' => 'localhost',
            'username' => 'travis_pass',
            'password' => 'wrong password',
            'adapter' => App\Component\Db\Adapter\PdoMysql::class,
            'options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                //, \PDO::ERRMODE_EXCEPTION => false
                \PDO::ATTR_CASE => \PDO::CASE_LOWER,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        ]
    ],
    'testpgsql' => [
        'nymfonya' => [
            'host' => 'localhost',
            'port' => 5432,
            'username' => 'travis_pass',
            'password' => 'some password',
            'adapter' => App\Component\Db\Adapter\PdoPgsql::class,
            'options' => []
        ],
        'badnymfonya' => [
            'host' => 'localhost',
            'port' => 5432,
            'username' => 'travis_pass',
            'password' => 'wrong password',
            'adapter' => App\Component\Db\Adapter\PdoPgsql::class,
            'options' => []
        ]
    ]
];
