<?php

namespace Tests\Model;

use PHPUnit\Framework\TestCase as PFT;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use App\Model\Accounts;
use Tests\Fake\Credential;

/**
 * @covers \App\Model\Accounts::<public>
 */
class AccountsTest extends PFT
{
    use Credential;

    const TEST_ENABLE = true;
    const CONFIG_PATH = '/../../config/';
    const ASSET_PATH = '/../../assets/tests/model/';
    const CSV_FILENAME = 'accounts.csv';

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
     * csv filename
     *
     * @var string
     */
    protected $filename;

    /**
     * instance
     *
     * @var Search
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
        $this->filename = realpath(
            __DIR__ . self::ASSET_PATH . self::CSV_FILENAME
        );
        $this->config = new Config(
            Config::ENV_CLI,
            __DIR__ . self::CONFIG_PATH
        );
        $this->container = new Container(
            $this->config->getSettings(Config::_SERVICES)
        );
        $this->instance = new Accounts($this->container);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        $this->instance = null;
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
        $class = new \ReflectionClass(Accounts::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        unset($class);
        return $method;
    }

    /**
     * testInstance
     * @covers App\Model\Accounts::__construct
     */
    public function testInstance()
    {
        $this->assertTrue($this->instance instanceof Accounts);
    }

    /**
     * testReadFromStream
     * @covers App\Model\Accounts::readFromStream
     */
    public function testReadFromStream()
    {
        $sft = $this->instance->setFilter('/^(.*),(.*),(.*),(.*),(.*)/');
        $this->assertTrue($sft instanceof Accounts);
        $sse = $this->instance->setseparator(',');
        $this->assertTrue($sse instanceof Accounts);
        $rff = $this->instance->readFromStream();
        $this->assertTrue($rff instanceof Accounts);
        $datas = $this->instance->get();
        $this->assertTrue(is_array($datas));
        $this->assertNotEmpty($datas);
        $this->assertTrue(count($datas[0]) === 6);
    }

    /**
     * testInit
     * @covers App\Model\Accounts::init
     */
    public function testInit()
    {
        $ini = self::getMethod('init')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue($ini instanceof Accounts);
    }

    /**
     * testGetAccountsFilename
     * @covers App\Model\Accounts::getAccountsFilename
     */
    public function testGetAccountsFilename()
    {
        $gaf = self::getMethod('getAccountsFilename')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_string($gaf));
        $this->assertNotEmpty($gaf);
    }

    /**
     * testSetItem
     * @covers App\Model\Accounts::setItem
     */
    public function testSetItem()
    {
        $ini = self::getMethod('setItem')->invokeArgs(
            $this->instance,
            [
                [0, 1, 2, 3, 4, 5]
            ]
        );
        $this->assertTrue($ini instanceof Accounts);
    }

    /**
     * testCreateFile
     * @covers App\Model\Accounts::createFile
     */
    public function testCreateFile()
    {
        @unlink($this->filename);
        $cfi = self::getMethod('createFile')->invokeArgs(
            $this->instance,
            [$this->filename]
        );
        $this->assertTrue($cfi instanceof Accounts);
    }

    /**
     * testToArray
     * @covers App\Model\Accounts::toArray
     */
    public function testToArray()
    {
        $toa = self::getMethod('toArray')->invokeArgs(
            $this->instance,
            []
        );
        $this->assertTrue(is_array($toa));
    }

    /**
     * testAuthBadLogin
     * @covers App\Model\Accounts::auth
     */
    public function testAuthBadLogin()
    {
        $aut = $this->instance->auth(
            $this->loginKo(),
            $this->passwordKo()
        );
        $this->assertTrue(is_array($aut));
        $this->assertEmpty($aut);
    }

    /**
     * testAuthBadPassword
     * @covers App\Model\Accounts::auth
     */
    public function testAuthBadPassword()
    {
        $aut = $this->instance->auth(
            $this->loginOk(),
            $this->passwordKo()
        );
        $this->assertTrue(is_array($aut));
        $this->assertEmpty($aut);
    }

    /**
     * testAuthOk
     * @covers App\Model\Accounts::auth
     */
    public function testAuthOk()
    {
        $aut = $this->instance->auth(
            $this->loginOk(),
            $this->passwordOk()
        );
        $this->assertTrue(is_array($aut));
        $this->assertNotEmpty($aut);
    }

    /**
     * testGetById
     * @covers App\Model\Accounts::getById
     */
    public function testGetById()
    {
        $gbi0 = $this->instance->getById(0);
        $this->assertTrue(is_array($gbi0));
        $this->assertEmpty($gbi0);
        $gbi1 = $this->instance->getById(1);
        $this->assertTrue(is_array($gbi1));
        $this->assertNotEmpty($gbi1);
    }
}
