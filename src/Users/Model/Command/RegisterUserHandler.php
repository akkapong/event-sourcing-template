<?php

declare(strict_types=1);

namespace Event\Users\Model\Command;

use Event\Users\Model\User;
use Event\Users\Model\UserRepository;
use Event\Users\Model\Command\RegisterUser;

class RegisterUserHandler
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RegisterUser $registerUser): void
    {
        $user = User::registerWithData($registerUser->id(), $registerUser->email(), $registerUser->password());
        $this->repository->save($user);
    }
}