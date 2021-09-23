<?php

namespace Remix\Oscillators;

use Remix\Oscillator;

/**
 * Remix Oscillator Max : this field must be less than or equal to the specified number of characters
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Max extends Oscillator
{
    public function run(string $value): bool
    {
        return (mb_strlen($value) <= (int)$this->option);
    }

    public function error(): string
    {
        if ((int)$this->option > 1) {
            return '{label} must be ' . $this->option . ' characters or less';
        } else {
            return '{label} must be zero or one character';
        }
    }
}
