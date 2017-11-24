<?php

declare(strict_types=1);

namespace Event\Users\Model\Command;

use Event\Users\Model\UserRepository;
use Event\Users\Model\Command\ChangeEmail;

class ChangeEmailHandler
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ChangeEmail $changeEmail): void
    {
        $user = $this->repository->get($changeEmail->id());
        $user->changeEmail($changeEmail->email());
        $this->repository->save($user);
    }
}