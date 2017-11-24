<?php

declare(strict_types=1);

namespace Event\Users\Model\Event;

use Prooph\EventSourcing\AggregateChanged;

class UserRegistered extends AggregateChanged
{
	public function __construct(string $aggregateId, array $payload, array $metadata = [])
	{
		parent::__construct($aggregateId, $payload, $metadata);
	}

    public function email(): string
    {
        return $this->payload['email'];
    }

    public function password(): string
    {
        return $this->payload['password'];
    }
}