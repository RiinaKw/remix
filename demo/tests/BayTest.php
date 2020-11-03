<?php

namespace Remix\AppTests;

use \Remix\Utility\Tests\TestCaseBase;

class BayTest extends TestCaseBase
{
    use \Remix\Utility\Tests\CaptureOutput;

    protected $remix = null;

    protected function setUp() : void
    {
        $this->remix = \Remix\App::getInstance()->initialize(__DIR__ . '/..');
    }

    public function testInstrument()
    {
        $response = $this->capture([$this->remix, 'runCli'], ['bay', 'instrument:acid', '-808', '--add=909']);

        $this->assertTrue($this->remix->isCli());
        $this->assertFalse($this->remix->isWeb());

        $this->assertMatchesRegularExpression('/303/', $response);
        $this->assertMatchesRegularExpression('/808/', $response);
        $this->assertMatchesRegularExpression('/909/', $response);
    }
}
