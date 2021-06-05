<?php

namespace Remix\Effector;

use Remix\Effector;

class Help extends Effector
{
    protected const TITLE = 'Remix command line client.';
    protected static $commands = [
        'version' => 'show version',
        'help' => 'amp help : this message',
    ];

    public function index()
    {
        $this->amp->play(['amp', 'version']);
        static::line('');
        $this->amp->availableCommands();
    }
}
// class Help
