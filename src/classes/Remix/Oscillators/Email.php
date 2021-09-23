<?php

namespace Remix\Oscillators;

use Remix\Oscillator;

/**
 * Remix Oscillator Email : this field must be a valid email address
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Email extends Oscillator
{
    public function run(string $value): bool
    {
        return preg_match('/[0-9A-Za-z_\-\.]+@[0-9A-Za-z_\-\.]+\.[a-z]+/', $value);
    }

    public function error(): string
    {
        return '{label} is invalid email address';
    }
}
