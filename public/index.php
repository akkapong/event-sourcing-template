<?php

// error_reporting(E_ALL);

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


try {

    /**
     * Define some useful constants
     */
    define('BASE_DIR', dirname(__DIR__));


    /**
     * Read auto-loader
     */
    include BASE_DIR . '/config/loader.php';

    /**
     * Read the configuration
     */
    $config  = include BASE_DIR . '/config/config.php';
    $message = include BASE_DIR . '/config/message.php';


    /**
     * Read services
     */
    include BASE_DIR . '/config/services.php';

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

} catch (Exception $e) {
    echo $e->getMessage(), '<br>';
    echo nl2br(htmlentities($e->getTraceAsString()));
}



