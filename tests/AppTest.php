<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateMethodBehavior;

    public function tearDown() : void
    {
        \Remix\App::destroy();
    }

    public function testInstance()
    {
        // is loadable?
        $this->assertTrue(class_exists('\Remix\App'));

        // is valid instance?
        $remix = \Remix\App::getInstance();
        $this->assertTrue((bool)$remix);
        $this->assertTrue($remix instanceof \Remix\App);
    }

    public function testBayInstance()
    {
        $remix = \Remix\App::getInstance();
        $bay = $this->invokeMethod($remix, 'bay', []);

        // is valid instance?
        $this->assertTrue((bool)$bay);
        $this->assertTrue($bay instanceof \Remix\Bay);

        $bay2 = $this->invokeMethod($remix, 'bay', []);
        $this->assertSame($bay, $bay2);
    }

    public function testMixerInstance()
    {
        $remix = \Remix\App::getInstance();
        $mixer = $this->invokeMethod($remix, 'mixer', []);

        // is valid instance?
        $this->assertTrue((bool)$mixer);
        $this->assertTrue($mixer instanceof \Remix\Mixer);

        $mixer2 = $this->invokeMethod($remix, 'mixer', []);
        $this->assertSame($mixer, $mixer2);
    }

    public function testDJInstance()
    {
        $remix = \Remix\App::getInstance();
        $dj = $this->invokeMethod($remix, 'dj', []);

        // is valid instance?
        $this->assertTrue((bool)$dj);
        $this->assertTrue($dj instanceof \Remix\DJ);

        $dj2 = $this->invokeMethod($remix, 'dj', []);
        $this->assertSame($dj, $dj2);
    }
}
