<?php

namespace RemixDemo\Tests;

use RemixDemo\TestCase\CliTestCase as TestCase;

/**
 * Test of Amp in the demo env.
 * @package  Demo\TestCase\Amp
 */
class AmpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Be sure to point to the app directory
        $this->initialize(__DIR__ . '/../..');
    }

    public function testNoArgs(): void
    {
        // is callable with no arguments?
        $this->expectOutputRegex('/Example of Effector/');
        $this->expectOutputRegex('/instrument:piano/');
        $this->expectOutputRegex('/livehouse:open/');

        $this->execute('amp');
    }
}
