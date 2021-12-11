<?php

namespace Remix\Tuners;

use Remix\Tuner;
use Remix\Exceptions\CoreException;

/**
 * Remix Tuner Cli : A constant that indicates whether it is a CLI.
 */
class Cli extends Tuner
{
    public function __construct(string $value)
    {
        parent::__construct($value === 'cli' ? 'cli' : 'web');
    }

    public function get(): string
    {
        return parent::get();
    }

    public function __get(string $key): bool
    {
        switch ($key) {
            case 'web':
            case 'cli':
                return $this->get() === $key;

            default:
                throw new CoreException("Attempted to access CliTuner with unknown key '{$key}'");
        }
    }
}
// class Tuner
