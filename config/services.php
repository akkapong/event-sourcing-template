<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Crypt;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;

use Phalcon\Mvc\Router;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Http\Client\Request as Curl;

use Event\Core\Validations\MyValidations;



/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Register the global configuration as config
 */
$di->set('config', $config);
$di->set('message', $message);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
}, true);

// Setup the view component
$di->set('view', function () {
    $view = new View();
    return $view;
});


/**
 * Initialise the mongo DB connection.
 */
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


$di->set('router', function ()
{
    $router = new Router();
    require 'routes.php';
    return $router;
});

// Register a "response" service in the container
$di->set('response', function () {
    $response = new Response();
    return $response;
});

// Register a "request" service in the container
$di->set('request', function () {
    $request = new Request();
    return $request;
});

// Register a "mongoLibrary" library in the container
$di->set('mongoLibrary', function () {
    return new \Event\Core\Libraries\MongoLibrary();
});

// Register a "curl" service in the container
$di->set('curl', function () {
    $curl = Curl::getProvider();
    return $curl;
});

// Register a "myValidate" service in the container
$di->set('myValidate', function () {
    $myValidate = new MyValidations();
    return $myValidate;
});

$di->set('commandBus', function() use ($config) {
    $commandBus = new Prooph\ServiceBus\CommandBus();
    require 'event.php';
    return $commandBus;
});

