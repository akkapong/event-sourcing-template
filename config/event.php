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


//Event 
$databaseConfig    = $config->database->connections->mysql;
$pdo               = new PDO("mysql:dbname=$databaseConfig->database;host=$databaseConfig->host", "$databaseConfig->username", "$databaseConfig->password");

$eventStore        = new MariaDbEventStore(new FQCNMessageFactory(), $pdo, new MariaDbAggregateStreamStrategy());
$eventEmitter      = new ProophActionEventEmitter();
$eventStore        = new ActionEventEmitterEventStore($eventStore, $eventEmitter);

$eventBus          = new EventBus($eventEmitter); 
$eventPublisher    = new EventPublisher($eventBus);
$eventPublisher->attachToEventStore($eventStore);

$pdoSnapshotStore  = new PdoSnapshotStore($pdo);

$eventRouter       = new CommandRouter();
//create route command
require 'command_route.php';
$eventRouter->attachToMessageBus($commandBus);

$eventRouter = new EventRouter();
//create route command
require 'event_route.php';
$eventRouter->attachToMessageBus($eventBus);

return $commandBus;