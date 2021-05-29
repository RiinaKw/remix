<?php

namespace Remix\Effector;

use Remix\Effector;

class Help extends Effector
{
    protected static $title = 'Remix Amp : command line client.';
    protected static $commands = [
        'version' => 'show version',
        'help' => 'amp help : this message',
    ];

    public function index()
    {
        \Remix\Amp::availableCommands();
    }
}
// class Help
