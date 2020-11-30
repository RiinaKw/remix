<?php

namespace App\Effector;

use Remix\Effector;

class User extends Effector
{
    public function index()
    {
        \Remix\DJ::truncate('users');
        \App\Vinyl\User::find(1);
    }
}
// class User
