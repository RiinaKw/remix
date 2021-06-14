<?php

namespace Remix\Preset;

/**
 * Effector settings
 *
 * @package  Remix\Core
 * @deprecated  No need to make it a class.
 */
final class Effector
{
    public const SHORTHANDLES = [
        '-v' => \Remix\Effector\Version::class,
        '-h' => \Remix\Effector\Help::class,
    ];
}
// class Effector
