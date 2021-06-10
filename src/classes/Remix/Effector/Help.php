<?php

namespace Remix\Effector;

use Remix\Effector;

class Help extends Effector
{
    public const TITLE = 'Remix command line client.';
    public const COMMANDS = [
        '' => 'show help message, you can optionally specify a command, like "help livehouse"',
    ];

    public function index($arg = [])
    {
        $target = $arg[0] ?? '';

        $this->amp->play(['amp', 'version']);
        static::line('');

        $this->amp->availableEffector($target);
    }
}
// class Help
