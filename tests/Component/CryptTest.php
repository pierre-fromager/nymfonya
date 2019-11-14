<?php

namespace Tests;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Component\Crypt;

/**
 * @covers \App\Component\Crypt::<public>
 */
class ComponentCyptTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../config/';
    const PAYLOAD = [0, 'gogo', 'dancer'];

    /**
     * config instance
     *
     * @var Config
     */
    protected $config;

    /**
     * instance
     *
     * @var Token
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
        $this->instance = new Crypt($this->config);
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
     * @covers App\Component\Crypt::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Crypt);
    }

    /**
     * testSetAlgo
     * @covers App\Component\Crypt::setAlgo
     */
    public function testSetAlgo()
    {
        $sa = $this->instance->setAlgo(Crypt::DEFAULT_ALGO);
        $this->assertTrue($sa instanceof Crypt);
    }

    /**
     * testEncrypt
     * @covers App\Component\Crypt::encrypt
     * @covers App\Component\Crypt::decrypt
     */
    public function testEncrypt()
    {
        $content = 'message';
        $en0 = $this->instance->encrypt($content, true);
        $this->assertNotEmpty($en0);
        $this->assertTrue(is_string($en0));
        $en1 = $this->instance->encrypt('', true);
        $this->assertNotEmpty($en1);
        $this->assertTrue(is_string($en1));
        $de0 = $this->instance->decrypt($en0, true);
        $this->assertEquals($content, $de0);
        $en2 = $this->instance->encrypt($content, false);
        $this->assertNotEmpty($en2);
        $this->assertTrue(is_string($en2));
        $en3 = $this->instance->encrypt('', false);
        $this->assertNotEmpty($en3);
        $this->assertTrue(is_string($en3));
        $de1 = $this->instance->decrypt($en2, false);
        $this->assertEquals($content, $de1);
    }

    /**
     * testDecryptException
     * @covers App\Component\Crypt::decrypt
     */
    public function testDecryptException()
    {
        $this->expectException(\Exception::class);
        $this->instance->decrypt('!test#', true);
    }

    /**
     * testSetAlgoException
     * @covers App\Component\Crypt::setAlgo
     */
    public function testSetAlgoException()
    {
        $this->expectException(\Exception::class);
        $this->instance->setAlgo('');
    }

    /**
     * testSetB64Key
     * @covers App\Component\Crypt::setB64Key
     */
    public function testSetB64Key()
    {
        $sbk = $this->instance->setB64Key('');
        $this->assertTrue($sbk instanceof Crypt);
    }

    /**
     * testGetVersionNumber
     * @covers App\Component\Crypt::getVersionNumber
     */
    public function testGetVersionNumber()
    {
        $gvn = $this->instance->getVersionNumber();
        $this->assertNotEmpty($gvn);
        $this->assertTrue(is_int($gvn));
    }

    /**
     * testGetVersionText
     * @covers App\Component\Crypt::getVersionText
     */
    public function testGetVersionText()
    {
        $gvn = $this->instance->getVersionText();
        $this->assertNotEmpty($gvn);
        $this->assertTrue(is_string($gvn));
    }
}
