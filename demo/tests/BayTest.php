<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class BayTest extends TestCase
{
    use \Remix\Utility\Tests\CaptureOutput;

    protected $remix = null;

    protected function setUp() : void
    {
        require_once(__DIR__ . '/../../vendor/autoload.php');
        $this->remix = \Remix\App::getInstance()->initialize(__DIR__ . '/..');
    }

    public function testInstrument()
    {
        $this->startCapture();
        $this->remix->runCli(['bay', 'instrument:acid', '-808', '--add=909']);
        $response = $this->endCapture();

        $this->assertTrue($this->remix->isCli());
        $this->assertFalse($this->remix->isWeb());

        $this->assertMatchesRegularExpression('/303/', $response);
        $this->assertMatchesRegularExpression('/808/', $response);
        $this->assertMatchesRegularExpression('/909/', $response);
    }
}
