
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('date.timezone', 'Europe/Paris');
ini_set('register_globals', 0);
ini_set('zlib.output_compression_level', 0);
//ini_set('session.save_path', realpath(__DIR__ . '/../session'));
ini_set('opcache.enable', 0);

if (function_exists('opcache_get_configuration')) {
    ini_set('opcache.memory_consumption', 1);
    ini_set('opcache.load_comments', true);
}

require_once '../vendor/autoload.php';

class Pipo
{
    private $n;
    private $a;
    public function __construct(string $name, int $age)
    {
        $this->n = $name;
        $this->a = $age;
    }

    public function show()
    {
        echo $this->n . ' , ' . $this->a . "\n";
    }
}

class Controller
{
    private $req;
    private $res;
    public function __construct(\App\Http\Request $req, \App\Http\Response $res)
    {
        $this->req = $req;
        $this->res = $res;
    }

    public function dump()
    {
        var_dump($this->req, $this->res);
    }
}

$servicesConfig = [
    //\App\Http\Request::class => [],
    //\App\Http\Response::class => [],
    \App\Config::class => [\App\Config::ENV_DEV],
    //\App\Kernel::class => [\App\Config::ENV_DEV],
    \Controller::class => [\App\Http\Request::class,\App\Http\Response::class],
    \Pipo::class => ['foo', 48],
    
    \Monolog\Handler\RotatingFileHandler::class => [
        __DIR__ . '/toto.txt',
        0,
        true,
        0664
    ],
    //\Monolog\Processor\WebProcessor::class => [],
    \Monolog\Logger::class => [
        \App\Config::_NAME,
        [\Monolog\Handler\RotatingFileHandler::class],
        [\Monolog\Processor\WebProcessor::class]
    ]
];


$sa = microtime(true);
$c = new \App\Container($servicesConfig);
/*
$c->getService(\Pipo::class)->show();
$c->getService(\Controller::class)->dump();
$c->getService(\Monolog\Logger::class)->debug('grogro');*/
$so = microtime(true);
//var_dump($c->getReporter());

echo sprintf('Elapsed %f', $so - $sa);
//var_dump($c->getServices());
