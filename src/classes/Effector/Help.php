<?php

namespace Remix\Effector;

use Remix\Effector;

class Help extends Effector
{
    protected const TITLE = 'Remix command line client.';
    protected static $commands = [
        '' => 'show help message, you can optionally specify a command, like "help livehouse"',
    ];

    public function index($arg = [])
    {
        $target = $arg[0] ?? '';

        $this->amp->play(['amp', 'version']);
        static::line('');

        $this->amp->availableCommands($target);
    }
}
// class Help
