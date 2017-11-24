<?php
namespace Event\Users\Collections;

use Phalcon\Mvc\MongoCollection;

class UserCollection extends MongoCollection
{
    public $uuid;
    public $email;
    public $password;

    public function getSource()
    {
        return 'users';
    }
}