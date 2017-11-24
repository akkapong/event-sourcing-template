<?php

use Phalcon\Config;
use Phalcon\Logger;

$database = include BASE_DIR . '/config/database.php';

return new Config([
    'application' => [
        'baseUri'         => getenv('BASE_URL', 'http://localhost.dev'),
    ],
    'database' => $database,

]);
