<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use \ReflectionMethod;
use App\Config;
use App\Container;
use App\Component\Model\Orm\Orm;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

/**
 * @covers \App\Component\Model\Orm\Orm::<public>
 */
class ComponentModelOrmTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';

    /**
     * config instance
     *
     * @var Config
     */
    protected $config;

    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * instance
     *
     * @var Orm
     */
    protected $instance;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $this->instance = new Orm($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
        $this->config = null;
        $this->request = null;
    }

    /**
     * get any method from a class to be invoked whatever the scope
     *
     * @param String $name
     * @return ReflectionMethod
     */
    protected static function getMethod(string $name): ReflectionMethod
    {
        $class = new \ReflectionClass(Orm::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Model\Orm\Orm::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Orm);
    }

    /**
     * testFind
     * @covers App\Component\Model\Orm\Orm::find
     */
    public function testFind()
    {
        $this->assertTrue($this->instance->find() instanceof Orm);
    }

    /**
     * testCount
     * @covers App\Component\Model\Orm\Orm::count
     */
    public function testCount()
    {
        $this->assertTrue($this->instance->count() instanceof Orm);
    }

    /**
     * testCountAlias
     * @covers App\Component\Model\Orm\Orm::count
     */
    public function testCountAlias()
    {
        $this->assertTrue(
            $this->instance->count(
                ['id'],
                ['id' => 'counterId']
            ) instanceof Orm
        );
    }

    /**
     * testInsert
     * @covers App\Component\Model\Orm\Orm::insert
     */
    public function testInsert()
    {
        $this->assertTrue(
            $this->instance->insert(['stuf' => 'value']) instanceof Orm
        );
    }

    /**
     * testUpdate
     * @covers App\Component\Model\Orm\Orm::update
     */
    public function testUpdate()
    {
        $this->assertTrue(
            $this->instance->update(
                ['stuf' => 'value'],
                ['id' => 2]
            ) instanceof Orm
        );
    }

    /**
     * testDelete
     * @covers App\Component\Model\Orm\Orm::delete
     */
    public function testDelete()
    {
        $this->assertTrue(
            $this->instance->delete(['id' => 1]) instanceof Orm
        );
    }

    /**
     * testGetSql
     * @covers App\Component\Model\Orm\Orm::getSql
     */
    public function testGetSql()
    {
        $this->instance->find();
        $this->assertTrue(is_string($this->instance->getSql()));
    }

    /**
     * testGetQueryBuilder
     * @covers App\Component\Model\Orm\Orm::getQueryBuilder
     */
    public function testGetQueryBuilder()
    {
        $this->assertTrue(
            $this->instance->getQueryBuilder() instanceof GenericBuilder
        );
    }

    /**
     * testGetQuery
     * @covers App\Component\Model\Orm\Orm::getQuery
     */
    public function testGetQuery()
    {
        $this->instance->find();
        $this->assertTrue(is_object($this->instance->getQuery()));
    }
}
