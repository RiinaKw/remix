<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class MixerTest extends TestCase
{
    use \Remix\Utility\Tests\CaptureOutput;

    protected $remix = null;

    protected function setUp() : void
    {
        require_once(__DIR__ . '/../../vendor/autoload.php');
        $this->remix = \Remix\App::getInstance()->initialize(__DIR__ . '/..');
    }

    public function testMixer()
    {
        $this->startCapture();
        $_SERVER['PATH_INFO'] = '/cb';
        $this->remix->runWeb();
        $response = $this->endCapture();

        $this->assertTrue($this->remix->isWeb());
        $this->assertFalse($this->remix->isCli());

        $this->assertMatchesRegularExpression('/from callback/', $response);
    }
}