<?php

namespace RemixDemo\Tests;

use RemixDemo\TestCase\CliTestCase as TestCase;

/**
 * Test of Amp in the demo env.
 * @package  Demo\TestCase\Amp
 */
class AmpTest extends TestCase
{
    public function testNoArgs(): void
    {
        // is callable with no arguments?
        $this->expectOutputRegex('/Example of Effector/');
        $this->expectOutputRegex('/instrument:piano/');
        $this->expectOutputRegex('/livehouse:open/');

        $this->execute('amp');
    }
}
