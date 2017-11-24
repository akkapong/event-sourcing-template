<?php
namespace Event\Users\UI\Controllers;

use Event\Core\Controllers\ControllerBase;
use Event\Users\Model\Command\RegisterUser;


use Event\Users\Model\Command\ChangeEmail;
use Event\Users\Model\Command\ChangeEmailHandler;
// use Event\Users\Model\Command\RegisterUser;
use Event\Users\Model\Command\RegisterUserHandler;
use Event\Users\Model\Event\EmailChanged;
use Event\Users\Model\Event\UserRegistered;
use Event\Users\Infrastructure\UserRepository;
use Event\Users\Projection\UserProjector;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Pdo\MariaDbEventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy\MariaDbAggregateStreamStrategy;
use Prooph\EventStore\Pdo\Projection\MariaDbProjectionManager;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
use Prooph\SnapshotStore\Pdo\PdoSnapshotStore;


/**
 * Display the default index page.
 */
class UserController extends ControllerBase
{
    //==== Start: Define variable ====//
    private $module = 'users';
    private $userService;
    private $modelName;
    private $schemaName;

    private $registerRule = [
        [
            'type'   => 'required',
            'fields' => ['email', 'password'],
        ],
    ];
    //==== End: Define variable ====//

    //==== Start: Support method ====//
    //Method for initial some variable
    public function initialize()
    {
        //TODO
    }

    //method for generate user id
    //TODO : need to convert int to string and call this method for generate unique  id 
    protected function generateUserId()
    {

    }

    //==== End: Support method ====//

    //==== Start: Main method ====//
    public function postRegisterAction()
    {
        //get inputuserId
        $params = $this->getPostInput();

        //define default
        $default = [
            "id" => (string)rand(1, 100), //TODO: For Temporary 
        ];

        // Validate 
        $params = $this->myValidate->validateApi($this->registerRule, $default, $params);

        if (isset($params['validate_error'])) {
            //Validate error
            return $this->responseError($params['validate_error'], '/users');
        }

        //TODO: create command to db
        $this->commandBus->dispatch(new RegisterUser([
            'id'       => $params['id'],
            'email'    => $params['email'],
            'password' => $params['password'],
        ]));

        return $this->output(json_encode(['id' => $params['id']]));
    }

   
    //==== End: Main method ====//
}
