<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class MixerTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;
    use \Remix\Utility\Tests\CaptureOutput;

    protected $remix = null;
    protected $public_dir = __DIR__ . '../public';

    protected function setUp() : void
    {
        $this->remix = \Remix\App::getInstance()->initialize(__DIR__ . '/..');
    }

    public function tearDown() : void
    {
        \Remix\App::destroy();
    }

    public function testMixer()
    {
        $_SERVER['PATH_INFO'] = '/';
        $response = $this->remix->runWeb($this->public_dir);
        $this->assertTrue($this->remix->isWeb());
        $this->assertFalse($this->remix->isCli());
        $this->assertMatchesRegularExpression('/Remix is ​​a lightweight PHP framework./', (string)$response);

        $_SERVER['PATH_INFO'] = '/cb';
        $response = $this->remix->runWeb($this->public_dir);
        $this->assertMatchesRegularExpression('/from callback/', (string)$response);

        $_SERVER['PATH_INFO'] = '/bounce';
        $response = $this->remix->runWeb($this->public_dir);
        $this->assertMatchesRegularExpression('/hello/', (string)$response);

        $_SERVER['PATH_INFO'] = '/bounce/hey,dj';
        $response = $this->remix->runWeb($this->public_dir);
        $this->assertMatchesRegularExpression('/hey,dj/', (string)$response);

        $_SERVER['PATH_INFO'] = '/redirect';
        $response = $this->remix->runWeb($this->public_dir);
        $status = $this->invokeProperty($response, 'status');
        $this->assertSame(303, $status);
        //$response->assertStatus(200);
    }
}
