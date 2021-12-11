<?php

namespace RemixDemo\TestCase;

/**
 * PHPUnit TestCase base class for CLI (Amp) in demo environment.
 *
 * @package  TestCase\Demo
 */
abstract class CliTestCase extends DemoTestCase
{
    /**
     * @property DAW $remixDaw
     */

    /**
     * Execute the Amp command.
     * @param  string $args  Arguments of the Effector
     */
    public function execute(string $args): void
    {
        $this->remixDaw->playCli(explode(' ', $args));
    }
}
