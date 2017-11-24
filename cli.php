<?php
use Phalcon\CLI\Console as ConsoleApp;
use Phalcon\DI\FactoryDefault\CLI as CliDI;


/**
 * Define some useful constants
 */
define('BASE_DIR', __DIR__);

// Using the CLI factory default services container
$di = new CliDI();
include __DIR__.'/vendor/autoload.php';

// Define path to application directory
// defined('APPLICATION_PATH')
// || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new \Phalcon\Loader();
$loader->registerDirs([
    __DIR__.'/src/Users/UI/Worker',
]);

$loader->registerNamespaces([
    'Phalcon' => BASE_DIR . '/../../vendor/phalcon/incubator/Library/',
]);

$loader->register();

/**
 * Environment variables
 */
$env = getenv('ENVIRONMENT');

if (empty($env)) {
    $env = 'docker';
} 


$dotenv = new Dotenv\Dotenv(BASE_DIR, ".$env.env");
$dotenv->load();


/**
 * Load the configuration all bank
 */
$configFile = __DIR__.'/config/config.php';

if (is_readable($configFile)) {
    $config = include $configFile;
    $di->set('config', $config);
}


$di->set('mongo', function () use ($config) {
    $m = $config->database->connections->mongo;

    if (!$m->username || !$m->password) {
        $dsn = 'mongodb://' . $m->host;
    } else {
        $dsn = sprintf(
            'mongodb://%s:%s@%s',
            $m->username,
            $m->password,
            $m->host
        );
    }

    $options = ['ssl' => false ];

    $mongo = new Phalcon\Db\Adapter\MongoDB\Client($dsn, $options);

    return $mongo->selectDatabase($m->database);
}, true);

// Collection Manager is required for MongoDB
$di->set('collectionManager', function () {
    return new Phalcon\Mvc\Collection\Manager();
}, true);


// Create a console application
$console = new ConsoleApp();
$console->setDI($di);

/**
 * Process the console arguments
 */
$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } else

    if ($k == 2) {
        $arguments['action'] = $arg;
    } else

    if ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

// define global constants for the current task and action
// define('CURRENT_TASK', (isset($argv['1']) ? $argv['1'] : null));
// define('CURRENT_ACTION', (isset($argv['2']) ? $argv['2'] : null));

$di->setShared('console', $console);



try {
    // handle incoming arguments
    // print_r($arguments); exit;
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}

