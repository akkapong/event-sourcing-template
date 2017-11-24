<?php

declare(strict_types=1);

namespace Event\Users\Projection;

use Event\Users\Model\Event\EmailChanged;
use Event\Users\Model\Event\UserRegistered;

use Event\Users\Collections\UserCollection;
use Event\Core\Repositories\CollectionRepositories;

class UserProjector extends CollectionRepositories
{
    //==== Start: Define variable ====//
    public $module         = 'users';
    public $collectionName = 'UserCollection';
    public $allowFilter    = ['uuid', 'email', 'password'];
    public $model;
    //==== Start: Define variable ====//


    //==== Start: Support method ====//
    public function __construct($model)
    {
        // $this->model = new UserCollection();
        // parent::__construct();
    }
    //==== End: Support method ====//


    public function onUserRegistered(UserRegistered $userRegistered): void
    {
        $this->model = new UserCollection();
        $params = [
            'uuid' => $userRegistered->aggregateId(),
            'email' => $userRegistered->email(),
            'password' => $userRegistered->password(),
        ];

        //insert
        $res = $this->insertData($params);

        // print_r($res);

        // $query = $this->PDO->prepare('INSERT INTO `read_users` SET email = ?, password = ?, id = ?');
        // $query->bindValue(1, $userRegistered->email());
        // $query->bindValue(2, $userRegistered->password());
        // $query->bindValue(3, $userRegistered->aggregateId());
        // $query->execute();

    }
    



    // private $PDO;

    // public function __construct(\PDO $PDO)
    // {
    //     $this->PDO = $PDO;
    // }

    // public function onUserRegistered(UserRegistered $userRegistered): void
    // {
    //     $query = $this->PDO->prepare('INSERT INTO `read_users` SET email = ?, password = ?, id = ?');
    //     $query->bindValue(1, $userRegistered->email());
    //     $query->bindValue(2, $userRegistered->password());
    //     $query->bindValue(3, $userRegistered->aggregateId());
    //     $query->execute();

    // }

    // public function onEmailChanged(EmailChanged $emailChanged): void
    // {
    //     $query = $this->PDO->prepare('UPDATE `read_users` SET email = ? WHERE id = ?');
    //     $query->bindValue(1, $emailChanged->email());
    //     $query->bindValue(2, $emailChanged->aggregateId());
    //     $query->execute();
    // }
}
