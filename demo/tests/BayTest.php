<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class BayTest extends TestCase
{
    protected $remix = null;

    protected function setUp() : void
    {
        $this->remix = \Remix\App::getInstance()->initialize(__DIR__ . '/..');
    }

    public function tearDown() : void
    {
        \Remix\App::destroy();
    }

    public function testNoArg()
    {
        $this->expectOutputRegex('/Remix Bay/');

        $this->remix->runCli(['bay']);
        $this->assertTrue($this->remix->isCli());
    }

    public function testInstrument()
    {
        $this->expectOutputRegex('/TB-303 and TR-808 and 909/');

        $this->remix->runCli(['bay', 'instrument:acid', '-808', '--add=909']);
    }
}
