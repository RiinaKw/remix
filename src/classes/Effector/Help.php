<?php

namespace Remix\Effector;

use Remix\Effector;

class Help extends Effector
{
    protected static $title = 'Remix command line client.';
    protected static $commands = [
        'version' => 'show version',
        'help' => 'amp help : this message',
    ];

    public function index()
    {
        static::line('Remix framework 0.5', 'black', 'green');
        static::line('by Riina K. <riinak.tv@gmail.com>', 'green');
        static::line('');
        \Remix\Amp::availableCommands();
    }
}
// class Help
