<?php

namespace Remix\DemoTests;

use Utility\Tests\CliTestCase;

class AmpTest extends CliTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->initialize(__DIR__ . '/../../../demo/app');
    }

    public function testNoArg(): void
    {
        // is callable with no arguments?
        $this->expectOutputRegex('/Example of Effector/');
        $this->expectOutputRegex('/instrument:piano/');
        $this->expectOutputRegex('/livehouse:open/');

        $this->execute('amp');
    }
}
