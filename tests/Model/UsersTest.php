<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Model\Users;

/**
 * @covers \App\Model\Users::<public>
 */
class AppModelUsersTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../config/';
    const VALID_LOGIN = 'admin@domain.tld';
    const VALID_PASSWORD = 'adminadmin';

    /**
     * config
     *
     * @var Config
     */
    protected $config;

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
    protected function setUp()
    {
        if (!self::TEST_ENABLE) {
            $this->markTestSkipped('Test disabled.');
        }
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->instance = new Users($this->config);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->instance = null;
    }

    /**
     * testInstance
     * @covers App\Model\Users::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Users);
    }

    /**
     * testGetById
     * @covers App\Model\Users::getById
     */
    public function testGetById()
    {
        $this->assertTrue(is_array($this->instance->getById(0)));
    }

    /**
     * testAuth
     * @covers App\Model\Users::auth
     */
    public function testAuth()
    {
        $auth0 = $this->instance->auth('', '');
        $this->assertTrue(is_array($auth0));
        $this->assertEmpty($auth0);
        $auth1 = $this->instance->auth(self::VALID_LOGIN, self::VALID_PASSWORD);
        $this->assertTrue(is_array($auth1));
        $this->assertNotEmpty($auth1);
    }
}
