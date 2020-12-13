<?php

namespace App\Effector;

use Remix\Effector;
use Remix\DJ;
use App\Vinyl;

class User extends Effector
{
    public function index()
    {
        Vinyl\User::table()->truncate();

        DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Tahiri']);
        DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Riina']);
        DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Riina']);


        $bpm = Vinyl\User::select()->where('name', 'Riina');
        $setlist = $bpm->prepare()->play()->asVinyl(Vinyl\User::class);

        var_dump($setlist->count());

        foreach ($setlist as $row) {
            var_dump($row);
        }

        var_dump(Vinyl\User::find(1));
    }
}
// class User
