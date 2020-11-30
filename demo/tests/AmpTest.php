<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class AmpTest extends TestCase
{
    protected $remix = null;

    protected function setUp(): void
    {
        $this->remix = \Remix\App::getInstance()->initialize(__DIR__ . '/..');
    }

    public function tearDown(): void
    {
        \Remix\App::destroy();
    }

    public function testNoArg()
    {
        $this->expectOutputRegex('/Remix Amp/');

        $this->remix->runCli(['amp']);
        $this->assertTrue($this->remix->isCli());
    }

    public function testInstrument()
    {
        $this->expectOutputRegex('/TB-303 and TR-808 and 909/');

        $this->remix->runCli(['amp', 'instrument:acid', '-808', '--add=909']);
    }
}
// class AmpTest
