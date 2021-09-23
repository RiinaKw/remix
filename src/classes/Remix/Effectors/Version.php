<?php

namespace Remix\Effectors;

use Remix\Effector;
use Remix\Audio;

/**
 * Remix Version Effector : show versions of Remix
 *
 * @package  Remix\CLI\Effectors
 * @todo Write the details.
 */
class Version extends Effector
{
    public const TITLE = 'Show version of Remix framework.';
    public const COMMANDS = [
        '' => 'show version',
    ];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function index($arg)
    {
        $preset = Audio::getInstance()->preset;
        static::line($preset->get('remix.title') . ' ' . $preset->get('remix.version'));
        static::line($preset->get('remix.author'));
    }
}
// class Version
