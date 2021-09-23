<?php

namespace Remix\Oscillators;

use Remix\Oscillator;

/**
 * Remix Oscillator Required : this field is required
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Required extends Oscillator
{
    public function run(string $value): bool
    {
        return ($value !== null && $value !== '');
    }

    public function error(): string
    {
        return '{key} : {label} is required';
    }
}
