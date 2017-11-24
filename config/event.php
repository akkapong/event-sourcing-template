<?php

use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Pdo\MariaDbEventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy\MariaDbAggregateStreamStrategy;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
use Prooph\SnapshotStore\Pdo\PdoSnapshotStore;

//@create use command
use Event\Users\Model\Command\ChangeEmail;
use Event\Users\Model\Command\ChangeEmailHandler;
use Event\Users\Model\Command\RegisterUser;
use Event\Users\Model\Command\RegisterUserHandler;
use Event\Users\Infrastructure\UserRepository;

//Event 
$databaseConfig    = $config->database->connections->mysql;
$pdo               = new PDO("mysql:dbname=$databaseConfig->database;host=$databaseConfig->host", "$databaseConfig->username", "$databaseConfig->password");
// $pdo               = new \PDO('mysql:dbname=prooph;host=maria', 'root', 'root');
$eventStore        = new MariaDbEventStore(new FQCNMessageFactory(), $pdo, new MariaDbAggregateStreamStrategy());
$eventEmitter      = new ProophActionEventEmitter();
$eventStore        = new ActionEventEmitterEventStore($eventStore, $eventEmitter);

$eventBus          = new EventBus($eventEmitter); 
$eventPublisher    = new EventPublisher($eventBus);
$eventPublisher->attachToEventStore($eventStore);

$pdoSnapshotStore  = new PdoSnapshotStore($pdo);

//@create repository
$userRepository    = new UserRepository($eventStore, $pdoSnapshotStore);

$eventRouter       = new CommandRouter();

//@create route command
$eventRouter->route(RegisterUser::class)->to(new RegisterUserHandler($userRepository));
$eventRouter->route(ChangeEmail::class)->to(new ChangeEmailHandler($userRepository));
$eventRouter->attachToMessageBus($commandBus);

return $commandBus;