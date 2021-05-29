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
        Effector::line('Remix framework 0.5');
        Effector::line('by Riina K.<riinak.tv@gmail.com>');
        Effector::line('');
        \Remix\Amp::availableCommands();
    }
}
// class Help
