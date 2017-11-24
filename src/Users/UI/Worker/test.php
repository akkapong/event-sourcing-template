<?php
namespace {

    use Event\Users\Model\Command\ChangeEmail;
    use Event\Users\Model\Command\ChangeEmailHandler;
    use Event\Users\Model\Command\RegisterUser;
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

    include "./../../../../vendor/autoload.php";

    $pdo = new PDO('mysql:dbname=prooph;host=maria', 'root', 'root');
    $eventStore = new MariaDbEventStore(new FQCNMessageFactory(), $pdo, new MariaDbAggregateStreamStrategy());
    $eventEmitter = new ProophActionEventEmitter();
    $eventStore = new ActionEventEmitterEventStore($eventStore, $eventEmitter);


//     ///
//     $di->set('mongo', function () use ($config) {
//     $m = $config->database->connections->mongo;

//     if (!$m->username || !$m->password) {
//         $dsn = 'mongodb://' . $m->host;
//     } else {
//         $dsn = sprintf(
//             'mongodb://%s:%s@%s',
//             $m->username,
//             $m->password,
//             $m->host
//         );
//     }

//     $options = ['ssl' => false ];

//     $mongo = new Phalcon\Db\Adapter\MongoDB\Client($dsn, $options);

//     return $mongo->selectDatabase($m->database);
// }, true);

// // Collection Manager is required for MongoDB
// $di->set('collectionManager', function () {
//     return new Phalcon\Mvc\Collection\Manager();
// }, true);
//     ///

    $eventBus = new EventBus($eventEmitter);
    $userProjector = new UserProjector();
    $eventRouter = new EventRouter();
    $eventRouter->route(EmailChanged::class)->to([$userProjector, 'onEmailChanged']);
    $eventRouter->route(UserRegistered::class)->to([$userProjector, 'onUserRegistered']);
    $eventRouter->attachToMessageBus($eventBus);

    while (true) {
    	echo ".";
    	// $a = $eventStore->load("Event\Users\Model\Event\UserRegistered");
    	$names = $eventStore->fetchStreamNames(null,null);
        print_r($names); exit;
    	foreach ($names as $name) {
    		// $event = $eventStore->load($name);
    		$argv = [
				'streamName'      => $name,
				'fromNumber'      => 1,
				'count'           => null,
				'metadataMatcher' => null,
	        ];
    		// $event = $eventEmitter->getNewActionEvent($eventStore::EVENT_APPEND_TO, $eventStore, $argv);
    		$event = $eventStore->load($name)->current();
    		// print_r($event->current()); exit;
    		$eventBus->dispatch($event);


    	}
    	sleep(1);
    	
    }
    

    // $eventBus = new EventBus($eventEmitter);
    // $eventPublisher = new EventPublisher($eventBus);
    // $eventPublisher->attachToEventStore($eventStore);

    // $pdoSnapshotStore = new PdoSnapshotStore($pdo);
    // $userRepository = new UserRepository($eventStore, $pdoSnapshotStore);

    // $projectionManager = new MariaDbProjectionManager($eventStore, $pdo);

    // // $commandBus = new CommandBus();
    // // $router = new CommandRouter();
    // // $router->route(RegisterUser::class)->to(new RegisterUserHandler($userRepository));
    // // $router->route(ChangeEmail::class)->to(new ChangeEmailHandler($userRepository));
    // // $router->attachToMessageBus($commandBus);

    // $userProjector = new UserProjector($pdo);
    // $eventRouter = new EventRouter();
    // $eventRouter->route(EmailChanged::class)->to([$userProjector, 'onEmailChanged']);
    // $eventRouter->route(UserRegistered::class)->to([$userProjector, 'onUserRegistered']);
    // $eventRouter->attachToMessageBus($eventBus);

    // $userId = '20';
	
}