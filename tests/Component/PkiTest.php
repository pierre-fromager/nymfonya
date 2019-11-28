<?php

namespace Tests\Component;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Component\Pki;

/**
 * @covers \App\Component\Pki::<public>
 */
class PkiTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../config/';
    const MESSAGE = 'test message';

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
     * @var Pki
     */
    protected $instance;

    /**
     * private key
     *
     * @var string
     */
    protected $privKey;

    /**
     * public key
     *
     * @var string
     */
    protected $pubKey;

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
        $this->instance = new Pki($this->container);
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
     * testInstance
     * @covers App\Component\Pki::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Pki);
    }

    /**
     * testGenerateKeyPair
     * @covers App\Component\Pki::generateKeyPair
     */
    public function testGenerateKeyPair()
    {
        $kpa = $this->instance->generateKeyPair();
        $this->privKey = $kpa[0];
        $this->pubKey = $kpa[1];
        $this->assertTrue(is_array($kpa));
        $this->assertNotEmpty($kpa);
        $this->assertTrue(is_string($this->privKey));
        $this->assertNotEmpty($this->privKey);
        $this->assertTrue(is_string($this->pubKey));
        $this->assertNotEmpty($this->pubKey);
    }

    /**
     * testEncrypt
     * @covers App\Component\Pki::generateKeyPair
     * @covers App\Component\Pki::encrypt
     * @covers App\Component\Pki::decrypt
     */
    public function testEncrypt()
    {
        $kpa = $this->instance->generateKeyPair();
        $this->privKey = $kpa[0];
        $this->pubKey = $kpa[1];
        $enc = $this->instance->encrypt(self::MESSAGE, $this->privKey);
        $this->assertTrue(is_string($enc));
        $this->assertNotEmpty($enc);
        $this->assertNotEquals(self::MESSAGE, $enc);
        $dec = $this->instance->decrypt($enc, $this->pubKey);
        $this->assertTrue(is_string($dec));
        $this->assertNotEmpty($dec);
        $this->assertEquals(self::MESSAGE, $dec);
    }

    /**
     * testValidate
     * @covers App\Component\Pki::validate
     */
    public function testValidate()
    {
        $kpa = $this->instance->generateKeyPair();
        $this->privKey = $kpa[0];
        $this->pubKey = $kpa[1];
        $enc = $this->instance->encrypt(self::MESSAGE, $this->privKey);
        $this->assertTrue(is_string($enc));
        $this->assertNotEmpty($enc);
        $this->assertNotEquals(self::MESSAGE, $enc);
        $val = $this->instance->validate(self::MESSAGE, $enc, $this->pubKey);
        $this->assertTrue(is_bool($val));
        $this->assertTrue($val);
    }
}
