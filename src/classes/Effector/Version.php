<?php

namespace Remix\Effector;

use Remix\Effector;

class Version extends Effector
{
    protected const TITLE = 'Show version of Remix framework.';
    protected static $commands = [
        '' => 'show version',
    ];

    public function index()
    {
        Effector::line('Remix framework 0.5');
        Effector::line('by Riina K.<riinak.tv@gmail.com>');
    }
}
// class Version
