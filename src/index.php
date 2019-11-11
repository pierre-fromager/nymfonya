<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('date.timezone', 'Europe/Paris');
ini_set('register_globals', 0);
ini_set('zlib.output_compression_level', 0);
ini_set('session.save_path', realpath(__DIR__ . '/../session'));
// ini_set('opcache.enable', 0); # opcache force enable in rare cases

if (function_exists('opcache_get_configuration')) {
    ini_set('opcache.memory_consumption', 2);
    ini_set('opcache.load_comments', '1');
}

require_once __DIR__ . '/../vendor/autoload.php';

$env = (php_sapi_name() === App\Config::ENV_CLI)
    ? App\Config::ENV_CLI
    : App\Config::ENV_DEV;

# Nymphonia bundle
$nymphoniaBundle = (new App\Kernel($env, __DIR__))
    ->setNameSpace('\\App\\Controllers\\');

try {
    $nymphoniaBundle->run()->send();
} catch (Exception $e) {
    $nymphoniaBundle->getLogger()->debug(
        'Exception : ' . $e->getMessage() . ' : ' . $e->getCode()
    );
}

unset($nymphoniaBundle);
