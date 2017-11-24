<?php

declare(strict_types=1);

namespace Event\Users\Infrastructure;

use Event\Users\Model\User;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\EventStore;
use Prooph\SnapshotStore\SnapshotStore;
use Event\Users\Model\UserRepository as BaseUserRepository;

class UserRepository extends AggregateRepository implements BaseUserRepository
{
    public function __construct(EventStore $eventStore, SnapshotStore $snapshotStore)
    {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass(User::class),
            new AggregateTranslator(),
            $snapshotStore,
            null,
            true
        );
    }

    protected function updateMetadata(User $user)
    {
        $aggregateId = $this->aggregateTranslator->extractAggregateId($user);
        $name        = $this->determineStreamName($aggregateId);
        $this->eventStore->updateStreamMetadata($name, ['status' => 'process']);
    
    }

    public function save(User $user): void
    {
        $this->saveAggregateRoot($user);
        //update meta
        $this->updateMetadata($user);
    }

    public function get(string $id): ?User
    {
        return $this->getAggregateRoot($id);
    }
}