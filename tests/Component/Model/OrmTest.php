<?php

namespace Tests\Component\Model;

use PHPUnit\Framework\TestCase as PFT;
use \ReflectionMethod;
use Nymfonya\Component\Config;
use App\Component\Container;
use App\Component\Model\Orm\Orm;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;
use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;

/**
 * @covers \App\Component\Model\Orm\Orm::<public>
 */
class OrmTest extends PFT
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
        $container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $this->instance = new class ($container) extends Orm
        {
            /**
             * table name
             * @var string
             */
            protected $tablename = 'testtable';

            /**
             * table primary key
             * @var string
             */
            protected $primary = 'id';

            /**
             * database name
             * @var string
             */
            protected $database = 'nymfonya';

            /**
             * pool slot name
             * @var string
             */
            protected $slot = 'test';

            /**
             * instanciate
             *
             * @param Container $container
             */
            public function __construct(Container $container)
            {
                parent::__construct($container);
            }
        };
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

    /**
     * testBuild
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuild()
    {
        $this->instance->find();
        $build = self::getMethod('build')->invokeArgs(
            $this->instance,
            [
                'testtable', [], []
            ]
        );
        $this->assertTrue($build instanceof Orm);
    }

    /**
     * testBuildInstanceException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildInstanceException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Build : Invalid query instance'
        );
        $build = self::getMethod('build')->invokeArgs(
            $this->instance,
            [
                'testtable', [], []
            ]
        );
        $this->assertTrue($build instanceof Orm);
    }

    /**
     * testBuildInvalidTypeException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildInvalidTypeException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Build : Invalid query type'
        );
        $mockOrm = $this->createMock(Orm::class);
        $mockOrm->method('getQuery')->willReturn(new \stdClass);
        self::getMethod('build')->invokeArgs($mockOrm, [
            'testtable', [], []
        ]);
    }

    /**
     * testBuildUpdateOk
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildUpdateOk()
    {
        $this->instance->setQuery(new Update());
        $build = self::getMethod('build')->invokeArgs($this->instance, [
            'tabletest', ['name' => 'test'], ['id' => 1]
        ]);
        $this->assertTrue($build instanceof Orm);
    }

    /**
     * testBuildUpdateEmptyException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildUpdateEmptyException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Build : Update requires not empty payload'
        );
        $this->instance->setQuery(new Update());
        self::getMethod('build')->invokeArgs($this->instance, [
            'tabletest', [], []
        ]);
    }

    /**
     * testBuildUpdateConditionException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildUpdateConditionException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Build : Update requires at least one condition'
        );
        $this->instance->setQuery(new Update());
        self::getMethod('build')->invokeArgs($this->instance, [
            'tabletest', ['id' => 1, 'name' => 'test'], []
        ]);
    }

    /**
     * testBuildInsertOk
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildInsertOk()
    {
        $this->instance->setQuery(new Insert());
        $build = self::getMethod('build')->invokeArgs($this->instance, [
            'tabletest', ['name' => 'test'], []
        ]);
        $this->assertTrue($build instanceof Orm);
    }

    /**
     * testBuildInsertEmptyException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildInsertEmptyException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Build : Insert requires not empty payload'
        );
        $this->instance->setQuery(new Insert());
        self::getMethod('build')->invokeArgs($this->instance, [
            'tabletest', [], []
        ]);
    }

    /**
     * testBuildDeleteOk
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildDeleteOk()
    {
        $this->instance->setQuery(new Delete());
        $build = self::getMethod('build')->invokeArgs($this->instance, [
            'tabletest', [], ['id' => 1]
        ]);
        $this->assertTrue($build instanceof Orm);
    }

    /**
     * testBuildDeleteConditionException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildDeleteConditionException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Build : Delete requires at least one condition'
        );
        $this->instance->setQuery(new Delete());
        self::getMethod('build')->invokeArgs($this->instance, [
            'tabletest', [], []
        ]);
    }

    /**
     * testBuildWhere
     * @covers App\Component\Model\Orm\Orm::buildWhere
     * @covers App\Component\Model\Orm\Orm::getQuery
     */
    public function testBuildWhere()
    {
        $this->instance->setQuery(new Select());
        $bwr0 = self::getMethod('buildWhere')->invokeArgs(
            $this->instance,
            [[]]
        );
        $this->assertTrue($bwr0 instanceof Orm);
        $bwr1 = self::getMethod('buildWhere')->invokeArgs(
            $this->instance,
            [['id>' => 100]]
        );
        $this->assertTrue($bwr1 instanceof Orm);
        $where = $this->instance->getQuery()->where();
        $this->assertTrue($where instanceof Where);
    }

    /**
     * testGetWhereOperator
     * @covers App\Component\Model\Orm\Orm::getWhereOperator
     */
    public function testGetWhereOperator()
    {
        $key = 'id';
        $opEqual = self::getMethod('getWhereOperator')->invokeArgs(
            $this->instance,
            [&$key, 1]
        );
        $this->assertTrue(is_string($opEqual));
        $this->assertEquals('equals', $opEqual);
        $opIn = self::getMethod('getWhereOperator')->invokeArgs(
            $this->instance,
            [&$key, [1, 2, 3]]
        );
        $this->assertTrue(is_string($opIn));
        $this->assertEquals('in', $opIn);
        $key = 'id!';
        $opNotIn = self::getMethod('getWhereOperator')->invokeArgs(
            $this->instance,
            [&$key, [1, 2, 3]]
        );
        $this->assertTrue(is_string($opNotIn));
        $this->assertEquals('notIn', $opNotIn);
        $key = 'id<';
        $opLt = self::getMethod('getWhereOperator')->invokeArgs(
            $this->instance,
            [&$key, 3]
        );
        $this->assertTrue(is_string($opLt));
        $this->assertEquals('lessThan', $opLt);
        $key = 'id>';
        $opGt = self::getMethod('getWhereOperator')->invokeArgs(
            $this->instance,
            [&$key, 3]
        );
        $this->assertTrue(is_string($opGt));
        $this->assertEquals('greaterThan', $opGt);
        $key = 'name#';
        $opLike = self::getMethod('getWhereOperator')->invokeArgs(
            $this->instance,
            [&$key, 3]
        );
        $this->assertTrue(is_string($opLike));
        $this->assertEquals('like', $opLike);
    }

    /**
     * testGetBuilderValues
     * @covers App\Component\Model\Orm\Orm::getBuilderValues
     */
    public function testGetBuilderValues()
    {
        $this->instance->find(['id', 'name'], ['id' => [1, 2, 3]]);
        $bindValues = $this->instance->getBuilderValues();
        $this->assertNotEmpty($bindValues);
        $this->assertTrue(is_array($bindValues));
    }

    /**
     * testSetOrder
     * @covers App\Component\Model\Orm\Orm::find
     * @covers App\Component\Model\Orm\Orm::setOrder
     */
    public function testSetOrder()
    {
        $this->instance->find(
            ['id', 'name'],
            ['id' => [1, 2, 3]],
            ['id' => 'ASC']
        );
        $sql0 = $this->instance->getSql();
        $this->assertTrue(strpos($sql0, 'ASC') > 0);
        $this->assertFalse(strpos($sql0, 'DESC') > 0);
        $this->instance->find(
            ['id', 'name'],
            ['id' => [1, 2, 3]],
            ['id' => 'DESC']
        );
        $sql0 = $this->instance->getSql();
        $this->assertTrue(strpos($sql0, 'DESC') > 0);
        $this->assertFalse(strpos($sql0, 'ASC') > 0);
    }

    /**
     * testGetPrimary
     * @covers App\Component\Model\Orm\Orm::getPrimary
     */
    public function testGetPrimary()
    {
        $pk = $this->instance->getPrimary();
        $this->assertTrue(is_string($pk));
        $this->assertNotEmpty($pk);
        $this->assertEquals($pk, 'id');
    }

    /**
     * testGetSlot
     * @covers App\Component\Model\Orm\Orm::getSlot
     */
    public function testGetSlot()
    {
        $slot = $this->instance->getSlot();
        $this->assertTrue(is_string($slot));
        $this->assertNotEmpty($slot);
        $this->assertEquals($slot, 'test');
    }

    /**
     * testGetContainter
     * @covers App\Component\Model\Orm\Orm::getContainer
     */
    public function testGetContainter()
    {
        $cont = $this->instance->getContainer();
        $this->assertTrue($cont instanceof Container);
    }

    /**
     * testGetDatabase
     * @covers App\Component\Model\Orm\Orm::getDatabase
     */
    public function testGetDatabase()
    {
        $dbname = $this->instance->getDatabase();
        $this->assertTrue(is_string($dbname));
        $this->assertNotEmpty($dbname);
        $this->assertEquals('nymfonya', $dbname);
    }
}
