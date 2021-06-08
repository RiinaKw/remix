<?php

namespace App\Effector;

use Remix\Effector;
use Remix\DJ;
use App\Vinyl;

class User extends Effector
{
    protected const TITLE = 'Example of Effector with DJ.';
    protected static $commands = [
        '' => 'show users, you can also search by --name',
        'init' => 'initialize user data for testing',
        'show' => 'show details of the specified user',
    ];

    public function index($arg = [])
    {
        $name = $arg['name'] ?? null;

        $bpm = Vinyl\User::select();
        if ($name) {
            $bpm->where('name', $name);
        }
        $setlist = $bpm->prepare()->play()->asVinyl(Vinyl\User::class);

        static::line('There are ' . $setlist->count() . ' data', 'green');

        foreach ($setlist as $row) {
            static::line('  ' . $row->id . ', ' . $row->name);
        }
    }

    public function show($arg)
    {
        $id = $arg[0] ?? null;
        $user = Vinyl\User::find($id);
        \Remix\Monitor::dump($user);
    }

    public function init()
    {
        Vinyl\User::table()->truncate();

        DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Tahiri']);
        DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Riina']);
        DJ::play('INSERT INTO users(name) VALUES(:name);', ['name' => 'Riina']);

        static::line('Initialized user data', 'green');
    }
}
// class User
