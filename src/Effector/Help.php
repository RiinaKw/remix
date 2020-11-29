<?php

namespace Remix\Effector;

use \Remix\Effector;

class Help extends Effector
{
    public function index()
    {
        Effector::line('Remix Amp : command line client.');
        Effector::line('amp version : show version');
        Effector::line('amp help : this message');
    }
} // class Help
