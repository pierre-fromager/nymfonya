<?php

namespace Tests\Component\Model;

use PHPUnit\Framework\TestCase as PFT;
use ReflectionMethod;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Model\Orm\Orm;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;
use NilPortugues\Sql\QueryBuilder\Manipulation\Insert;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;
use NilPortugues\Sql\QueryBuilder\Syntax\Where;
use App\Component\Model\Orm\InvalidQueryException;
use App\Component\Model\Orm\InvalidQueryUpdateException;
use App\Component\Model\Orm\InvalidQueryInsertException;
use App\Component\Model\Orm\InvalidQueryDeleteException;

/**
 * @covers \App\Component\Model\Orm\Orm::<public>
 */
class OrmTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';
    const DB_NAME = 'covid';
    const _BUILD = 'build';
    const GET_WHERE_OPERATOR = 'getWhereOperator';

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
    protected function setUp(): void
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
            protected $database = 'covid';

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
    protected function tearDown(): void
    {
        $this->instance = null;
        $this->config = null;
        $this->request = null;
        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
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
                ['id' => '*'],
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
        $insert = $this->instance->insert(['stuf' => 'value']);
        $this->assertTrue($insert instanceof Orm);
    }

    /**
     * testUpdate
     * @covers App\Component\Model\Orm\Orm::update
     */
    public function testUpdate()
    {
        $update = $this->instance->update(['stuf' => 'value'], ['id' => 2]);
        $this->assertTrue($update instanceof Orm);
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
        $gqb = $this->instance->getQueryBuilder();
        $this->assertTrue($gqb instanceof GenericBuilder);
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
        $build = self::getMethod(self::_BUILD)->invokeArgs(
            $this->instance,
            ['testtable', [], []]
        );
        $this->assertTrue($build instanceof Orm);
    }

    /**
     * testBuildInstanceException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildInstanceException()
    {
        $this->expectException(InvalidQueryException::class);
        $this->expectExceptionMessage(InvalidQueryException::MSG_INSTANCE);
        $this->expectExceptionCode(10);
        $build = self::getMethod(self::_BUILD)->invokeArgs(
            $this->instance,
            ['testtable', [], []]
        );
        $this->assertTrue($build instanceof Orm);
    }

    /**
     * testBuildInvalidTypeException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildInvalidTypeException()
    {
        $this->expectException(InvalidQueryException::class);
        $this->expectExceptionMessage(InvalidQueryException::MSG_TYPE);
        $this->expectExceptionCode(10);
        $mockOrm = $this->createMock(Orm::class);
        $mockOrm->method('getQuery')->willReturn(new \stdClass());
        self::getMethod(self::_BUILD)->invokeArgs($mockOrm, [
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
        $build = self::getMethod(self::_BUILD)->invokeArgs(
            $this->instance,
            ['tabletest', ['name' => 'test'], ['id' => 1]]
        );
        $this->assertTrue($build instanceof Orm);
    }

    /**
     * testBuildUpdateEmptyException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildUpdateEmptyException()
    {
        $this->expectException(InvalidQueryUpdateException::class);
        $this->expectExceptionMessage(
            InvalidQueryUpdateException::MSG_PAYLOAD
        );
        $this->expectExceptionCode(11);
        $this->instance->setQuery(new Update());
        self::getMethod(self::_BUILD)->invokeArgs(
            $this->instance,
            ['tabletest', [], []]
        );
    }

    /**
     * testBuildUpdateConditionException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildUpdateConditionException()
    {
        $this->expectException(InvalidQueryUpdateException::class);
        $this->expectExceptionMessage(
            InvalidQueryUpdateException::MSG_CONDITION
        );
        $this->expectExceptionCode(11);
        $this->instance->setQuery(new Update());
        self::getMethod(self::_BUILD)->invokeArgs(
            $this->instance,
            ['tabletest', ['id' => 1, 'name' => 'test'], []]
        );
    }

    /**
     * testBuildInsertOk
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildInsertOk()
    {
        $this->instance->setQuery(new Insert());
        $build = self::getMethod(self::_BUILD)->invokeArgs(
            $this->instance,
            ['tabletest', ['name' => 'test'], []]
        );
        $this->assertTrue($build instanceof Orm);
    }

    /**
     * testBuildInsertEmptyException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildInsertEmptyException()
    {
        $this->expectException(InvalidQueryInsertException::class);
        $this->expectExceptionMessage(
            InvalidQueryInsertException::MSG_PAYLOAD
        );
        $this->expectExceptionCode(12);
        $this->instance->setQuery(new Insert());
        self::getMethod(self::_BUILD)->invokeArgs(
            $this->instance,
            ['tabletest', [], []]
        );
    }

    /**
     * testBuildDeleteOk
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildDeleteOk()
    {
        $this->instance->setQuery(new Delete());
        $build = self::getMethod(self::_BUILD)->invokeArgs(
            $this->instance,
            ['tabletest', [], ['id' => 1]]
        );
        $this->assertTrue($build instanceof Orm);
    }

    /**
     * testBuildDeleteConditionException
     * @covers App\Component\Model\Orm\Orm::build
     */
    public function testBuildDeleteConditionException()
    {
        $this->expectException(InvalidQueryDeleteException::class);
        $this->expectExceptionMessage(
            InvalidQueryDeleteException::MSG_CONDITION
        );
        $this->expectExceptionCode(13);
        $this->instance->setQuery(new Delete());
        self::getMethod(self::_BUILD)->invokeArgs($this->instance, [
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
     * testBuildWhereException
     * @covers App\Component\Model\Orm\Orm::buildWhere
     */
    public function testBuildWhereException()
    {
        $this->expectException(\Exception::class);
        $this->instance->setQuery(new Select());
        self::getMethod('buildWhere')->invokeArgs(
            $this->instance,
            [['id']]
        );
    }

    /**
     * testGetWhereOperator
     * @covers App\Component\Model\Orm\Orm::getWhereOperator
     */
    public function testGetWhereOperator()
    {
        $key = 'id';
        $opEqual = self::getMethod(self::GET_WHERE_OPERATOR)->invokeArgs(
            $this->instance,
            [&$key, 1]
        );
        $this->assertTrue(is_string($opEqual));
        $this->assertEquals('equals', $opEqual);
        $opIn = self::getMethod(self::GET_WHERE_OPERATOR)->invokeArgs(
            $this->instance,
            [&$key, [1, 2, 3]]
        );
        $this->assertTrue(is_string($opIn));
        $this->assertEquals('in', $opIn);
        $key = 'id!';
        $opNotIn = self::getMethod(self::GET_WHERE_OPERATOR)->invokeArgs(
            $this->instance,
            [&$key, [1, 2, 3]]
        );
        $this->assertTrue(is_string($opNotIn));
        $this->assertEquals('notIn', $opNotIn);
        $key = 'id<';
        $opLt = self::getMethod(self::GET_WHERE_OPERATOR)->invokeArgs(
            $this->instance,
            [&$key, 3]
        );
        $this->assertTrue(is_string($opLt));
        $this->assertEquals('lessThan', $opLt);
        $key = 'id>';
        $opGt = self::getMethod(self::GET_WHERE_OPERATOR)->invokeArgs(
            $this->instance,
            [&$key, 3]
        );
        $this->assertTrue(is_string($opGt));
        $this->assertEquals('greaterThan', $opGt);
        $key = 'name#';
        $opLike = self::getMethod(self::GET_WHERE_OPERATOR)->invokeArgs(
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
        $this->assertEquals(self::DB_NAME, $dbname);
    }

    /**
     * testGetTable
     * @covers App\Component\Model\Orm\Orm::getTable
     */
    public function testGetTable()
    {
        $tableName = $this->instance->getTable();
        $this->assertTrue(is_string($tableName));
        $this->assertNotEmpty($tableName);
        $this->assertEquals('testtable', $tableName);
    }

    /**
     * testResetBuilder
     * @covers App\Component\Model\Orm\Orm::resetBuilder
     */
    public function testResetBuilder()
    {
        $this->assertTrue(
            $this->instance->resetBuilder() instanceof Orm
        );
        $this->assertTrue(
            $this->instance->getQueryBuilder() instanceof GenericBuilder
        );
    }
}
