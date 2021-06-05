<?php

namespace Remix\Effector;

use Remix\Effector;
use Remix\Audio;

class Version extends Effector
{
    protected const TITLE = 'Show version of Remix framework.';
    protected static $commands = [
        '' => 'show version',
    ];

    public function index()
    {
        $preset = Audio::getInstance()->preset;
        static::line($preset->get('remix.title') . ' ' . $preset->get('remix.version'));
        static::line($preset->get('remix.author'));
    }
}
// class Version
