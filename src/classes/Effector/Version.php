<?php

namespace Remix\Effector;

use Remix\Effector;
use Remix\Audio;

class Version extends Effector
{
    public const TITLE = 'Show version of Remix framework.';
    public const COMMANDS = [
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
