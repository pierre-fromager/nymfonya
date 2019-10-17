<?php
error_reporting(E_ERROR);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('date.timezone', 'Europe/Paris');
ini_set('register_globals', 0);
ini_set('opcache.enable', 1);
echo 'Booting...' . "\n";
$there = __DIR__;
$loader = require $there . '/../vendor/autoload.php';
$loader->add('Tests', $there);
echo 'Testing...' . "\n";
