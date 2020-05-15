<?php

namespace Remix\Effector;

use \Remix\Effector;

class Help extends Effector
{
    public function index()
    {
        Effector::line('Remix Bay : command line client.');
        Effector::line('bay version : show version');
        Effector::line('bay help : this message');
    }
} // class Help
