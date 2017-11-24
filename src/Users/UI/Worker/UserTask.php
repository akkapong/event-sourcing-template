<?php
use Phalcon\DI;
use Phalcon\DI\InjectionAwareInterface;
use Phalcon\Cli\Task;

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
use Event\Users\Collections\UserCollection;
use Prooph\EventStore\Metadata\MetadataMatcher;
use Prooph\EventStore\Metadata\Operator;

class UserTask extends Task implements InjectionAwareInterface
{
    //====== Start: Define parameter =======//
    private $eventStore;
    private $eventBus;
    //====== End: Define parameter =======//

    //====== Start: Support Method =======//
    //Method for init 
    protected function init()
    {
        $pdo = new PDO('mysql:dbname=prooph;host=maria', 'root', 'root');
        $eventStore = new MariaDbEventStore(new FQCNMessageFactory(), $pdo, new MariaDbAggregateStreamStrategy());
        $eventEmitter = new ProophActionEventEmitter();
        $this->eventStore = new ActionEventEmitterEventStore($eventStore, $eventEmitter);

        $this->eventBus = new EventBus($eventEmitter);
        $this->createRoute($this->eventBus);
    }

    //methiod for create route
    protected function createRoute($eventBus)
    {
        $userProjector = new UserProjector(new UserCollection());
        $eventRouter = new EventRouter();
        // $eventRouter->route(EmailChanged::class)->to([$userProjector, 'onEmailChanged']);
        $eventRouter->route(UserRegistered::class)->to([$userProjector, 'onUserRegistered']);
        $eventRouter->attachToMessageBus($eventBus);
    }
    //====== End: Support Method =======//

    //====== Start: Main Method =======//
    //Method for check merchat status
    public function registerAction(array $params)
    {
        $this->init();
        
        while (true) {
            echo ".";
            // $a = $this->eventStore->load("Event\Users\Model\Event\UserRegistered");
            $metadata = new MetadataMatcher;
            $oper = Operator::byName('EQUALS');
            $names = $this->eventStore->fetchStreamNames(null, $metadata->withMetadataMatch('status', $oper, 'process'));

            foreach ($names as $name) {
                // $event = $eventStore->load($name);
                // $argv = [
                //     'streamName'      => $name,
                //     'fromNumber'      => 1,
                //     'count'           => null,
                //     'metadataMatcher' => null,
                // ];
                // $event = $eventEmitter->getNewActionEvent($eventStore::EVENT_APPEND_TO, $eventStore, $argv);
                $event = $this->eventStore->load($name)->current();
                
                $this->eventBus->dispatch($event);

                //update meta data
                $this->eventStore->updateStreamMetadata($name, ['status' => 'finished']);



            }
            sleep(1);
            
        }
        return true;
    }
    //====== End: Main Method =======//
}