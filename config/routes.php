<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
// $router = new Phalcon\Mvc\Router();

// $router->addGet("/basic", "Index::basic");
// $router->addGet("/basic-list", "Index::basicList");
// $router->addGet("/test-mongo", "test::mongoInsert");

// return $router;

use Phalcon\Mvc\Router\Group as RouterGroup;

$router->removeExtraSlashes(true);

$router->setDefaults(array(
    'namespace'  => 'Event\Core\Controllers',
    'controller' => 'error',
    'action'     => 'page404'
));

//==========Route for api==========
$api = new RouterGroup(array(
    'namespace' => 'Event\Users\UI\Controllers'
));

//==== Start : user Section ====//
//@create route
$api->addPost('/user/register', [
    'controller' => 'user',
    'action'     => 'postRegister',
]);



//==== End : user Section ====//

//==== End : Deal Section ====//

$router->mount($api);

$api = new RouterGroup(array(
    'namespace' => 'Event\Notes\UI\Controllers'
));

//@create route
$api->addPost('/api/note', [
    'controller' => 'note',
    'action'     => 'postNote',
]);

$router->mount($api);

return $router;
