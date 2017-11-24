<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces([
    'Phalcon' => BASE_DIR . '/../../vendor/phalcon/incubator/Library/',
]);

$loader->register();

// Use composer autoloader to load vendor classes
require_once BASE_DIR . '/vendor/autoload.php';

/**
 * Environment variables
 */
$env = getenv('ENVIRONMENT');

if (empty($env)) {
    $env = 'docker';
} 


$dotenv = new Dotenv\Dotenv(BASE_DIR, ".$env.env");
$dotenv->load();

// if (getenv('APP_ENV') !== 'production') {
//     $whoops = new \Whoops\Run;
//     $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
//     $whoops->register();
// }


