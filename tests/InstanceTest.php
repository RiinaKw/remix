<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class InstanceTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    protected $remix = null;

    public function setUp(): void
    {
        $this->remix = \Remix\App::getInstance();
    }

    public function tearDown(): void
    {
        \Remix\App::destroy();
    }

    public function testAmpInstance(): void
    {
        $amp = $this->invokeMethod($this->remix, 'amp', []);

        // is valid instance?
        $this->assertTrue((bool)$amp);
        $this->assertTrue($amp instanceof \Remix\Amp);

        $amp2 = $this->invokeMethod($this->remix, 'amp', []);
        $this->assertSame($amp, $amp2);
    }

    public function testMixerInstance(): void
    {
        $mixer = $this->invokeMethod($this->remix, 'mixer', []);

        // is valid instance?
        $this->assertTrue((bool)$mixer);
        $this->assertTrue($mixer instanceof \Remix\Mixer);

        $mixer2 = $this->invokeMethod($this->remix, 'mixer', []);
        $this->assertSame($mixer, $mixer2);
    }

    public function testDJInstance(): void
    {
        $dj = $this->invokeMethod($this->remix, 'dj', []);

        // is valid instance?
        $this->assertTrue((bool)$dj);
        $this->assertTrue($dj instanceof \Remix\DJ);

        $dj2 = $this->invokeMethod($this->remix, 'dj', []);
        $this->assertSame($dj, $dj2);
    }
}
// class InstanceTest
