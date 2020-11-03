<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class RemixDemoBayTest extends TestCase
{
    use \Remix\Utility\Tests\CaptureOutput;

    protected $remix = null;

    protected function setUp() : void
    {
        require_once(__DIR__ . '/../../vendor/autoload.php');
        $this->remix = \Remix\App::getInstance()->initialize(__DIR__ . '/../demo');
    }

    public function testInstrument()
    {
        $this->startCapture();
        $this->remix->runCli(['bay', 'instrument:acid', '-808', '--add=909']);
        $result = $this->endCapture();

        $this->assertMatchesRegularExpression('/303/', $result);
        $this->assertMatchesRegularExpression('/808/', $result);
        $this->assertMatchesRegularExpression('/909/', $result);
    }
}
