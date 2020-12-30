<?php

namespace Tests\Model\Repository;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Model\Orm\Orm;
use App\Component\Model\Orm\IOrm;
use App\Model\Repository\Users;

/**
 * @covers \App\Model\Repository\Users::<public>
 */
class UsersTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';
    const VALID_LOGIN = 'admin@domain.tld';
    const VALID_PASSWORD = 'adminadmin';

    /**
     * config
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
     * @var Users
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
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $this->instance = new Users($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->instance = null;
    }

    /**
     * testInstance
     * @covers App\Model\Repository\Users::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Users);
        $this->assertEquals(
            get_parent_class($this->instance),
            Orm::class
        );
        $this->assertTrue(in_array(
            IOrm::class,
            class_implements(Users::class)
        ));
    }

    /**
     * testGetPrimary
     * @covers App\Model\Repository\Users::getPrimary
     */
    public function testGetPrimary()
    {
        $this->assertTrue($this->instance instanceof Users);
        $this->assertEquals(
            $this->instance->getPrimary(),
            'id'
        );
    }

    /**
     * testGetById
     * @covers App\Model\Repository\Users::getById
     * @covers App\Model\Repository\Users::getSql
     * @covers App\Model\Repository\Users::getBuilderValues
     */
    public function testGetById()
    {
        $this->assertTrue(
            $this->instance->getById(0) instanceof Users
        );
        $this->assertEquals(
            $this->instance->getSql(),
            'SELECT users.* FROM users WHERE (users.id = :v1) ORDER BY users.id DESC'
        );
        $this->assertEquals(
            $this->instance->getBuilderValues(),
            [':v1' => 0]
        );
    }

    /**
     * testGetByEmail
     * @covers App\Model\Repository\Users::getByEmail
     * @covers App\Model\Repository\Users::getSql
     * @covers App\Model\Repository\Users::getBuilderValues
     */
    public function testGetByEmail()
    {
        $this->assertTrue(
            $this->instance->getByEmail('admin@domain.tld')
                instanceof Users
        );
        $this->assertEquals(
            $this->instance->getSql(),
            'SELECT users.* FROM users WHERE (users.email = :v1) ORDER BY users.id DESC'
        );
        $this->assertEquals(
            $this->instance->getBuilderValues(),
            [':v1' => 'admin@domain.tld']
        );
    }
}
