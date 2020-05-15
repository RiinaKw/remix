<?php

namespace Remix\Effector;

use \Remix\Effector;

class Version extends Effector
{
    public function index()
    {
        Effector::line('Remix framework 0.3');
        Effector::line('by Riina K.<riinak.tv@gmail.com>');
    }
} // class Version
