<?php

namespace App\Effector;

use \Remix\Effector;

class User extends Effector
{

    public function index($arg)
    {
        \Remix\DJ::truncate('users');
        $result = \App\Vinyl\User::find(1);
    }
} // class User
