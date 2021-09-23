<?php

namespace Remix;

/**
 * Remix Oscillator : rule definition of a input item
 *
 * @package  Remix\Web\Form
 * @see \Remix\Filter
 * @todo Write the details.
 */
abstract class Oscillator extends Gear
{
    protected $option = null;

    public function __construct($option = null)
    {
        parent::__construct();
        $this->option = $option;
    }

    /**
     * Run validation
     * @param  string $value  input of item
     * @return bool           validation result
     */
    abstract public function run(string $value): bool;

    /**
     * Get the error message
     * @return string  message
     */
    abstract public function error(): string;
}
