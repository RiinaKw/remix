<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class MixerTest extends TestCase
{
    use \Remix\Utility\Tests\CaptureOutput;

    protected $remix = null;

    protected function setUp() : void
    {
        $this->remix = \Remix\App::getInstance()->initialize(__DIR__ . '/..');
    }

    public function testMixer()
    {
        $_SERVER['PATH_INFO'] = '/cb';
        $response = $this->capture([$this->remix, 'runWeb']);

        $this->assertTrue($this->remix->isWeb());
        $this->assertFalse($this->remix->isCli());

        $this->assertMatchesRegularExpression('/from callback/', $response);
    }
}
