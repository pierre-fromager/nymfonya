<?php

namespace Tests\Component\Jwt;

use PHPUnit\Framework\TestCase as PFT;
use App\Config;
use App\Component\Http\Request;
use App\Component\Jwt\Token;

/**
 * @covers \App\Component\Jwt\Token::<public>
 */
class TokenTest extends PFT
{

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../../config/';
    const PAYLOAD = [0, 'gogo', 'dancer'];

    /**
     * config instance
     *
     * @var Config
     */
    protected $config;

    /**
     * request instance
     *
     * @var Request
     */
    protected $request;

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
        $this->request = new Request();
        $this->instance = new Token($this->config, $this->request);
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
     * @return void
     */
    protected static function getMethod(string $name)
    {
        $class = new \ReflectionClass(Token::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Component\Jwt\Token::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Token);
    }

    /**
     * testEncode
     * @covers App\Component\Jwt\Token::encode
     */
    public function testEncode()
    {
        $te = $this->instance->encode(0, 'gogo', 'dancer');
        $this->assertTrue(is_string($te));
        $this->assertNotEmpty($te);
    }

    /**
     * testGetConfig
     * @covers App\Component\Jwt\Token::getConfig
     */
    public function testGetConfig()
    {
        $tc = self::getMethod('getConfig')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($tc));
    }

    /**
     * testGetConfigSecret
     * @covers App\Component\Jwt\Token::getConfigSecret
     */
    public function testGetConfigSecret()
    {
        $gcs = self::getMethod('getConfigSecret')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($gcs));
        $this->assertNotEmpty($gcs);
    }

    /**
     * testGetConfigAlgo
     * @covers App\Component\Jwt\Token::getConfigAlgo
     */
    public function testGetConfigAlgo()
    {
        $gca = self::getMethod('getConfigAlgo')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($gca));
        $this->assertNotEmpty($gca);
    }

    /**
     * testSetGetToken
     * @covers App\Component\Jwt\Token::getToken
     * @covers App\Component\Jwt\Token::setToken
     */
    public function testSetGetToken()
    {
        $gca = self::getMethod('getToken')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertEmpty($gca);
        $gcane = self::getMethod('setToken')->invokeArgs(
            $this->instance,
            ['tokentestcontent']
        );
        $this->assertNotEmpty($gcane);
    }

    /**
     * testGetToEncodePayload
     * @covers App\Component\Jwt\Token::getToEncodePayload
     */
    public function testGetToEncodeData()
    {
        $gted = self::getMethod('getToEncodePayload')->invokeArgs(
            $this->instance,
            self::PAYLOAD
        );
        $this->assertTrue(is_array($gted));
    }

    /**
     * testGetToEncodePayload
     * @covers App\Component\Jwt\Token::encode
     * @covers App\Component\Jwt\Token::decode
     */
    public function testDecode()
    {
        $this->instance->setIssueAt(time());
        $this->instance->setIssueAtDelay(-100);
        $this->instance->setTtl(1200);
        $te = $this->instance->encode(0, 'gogo', 'dancer');
        $this->assertTrue(is_string($te));
        $this->assertNotEmpty($te);
        $dot = $this->instance->decode($te);
        $this->assertTrue(is_object($dot));
    }
}
