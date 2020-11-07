<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class BayTest extends TestCase
{
    use \Remix\Utility\Tests\CaptureOutput;

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
        $response = $this->capture([$this->remix, 'runCli'], ['bay']);

        $this->assertTrue($this->remix->isCli());
        $this->assertFalse($this->remix->isWeb());

        $this->assertMatchesRegularExpression('/Remix Bay/', $response);
    }

    public function testInstrument()
    {
        $response = $this->capture([$this->remix, 'runCli'], ['bay', 'instrument:acid', '-808', '--add=909']);

        $this->assertMatchesRegularExpression('/303/', $response);
        $this->assertMatchesRegularExpression('/808/', $response);
        $this->assertMatchesRegularExpression('/909/', $response);
    }
}
