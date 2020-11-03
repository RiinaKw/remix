<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateMethodBehavior;

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
    }

    public function testMixerInstance()
    {
        $remix = \Remix\App::getInstance();
        $mixer = $this->invokeMethod($remix, 'mixer', []);

        // is valid instance?
        $this->assertTrue((bool)$mixer);
        $this->assertTrue($mixer instanceof \Remix\Mixer);
    }
}
