<?php

namespace Remix\CoreTests;

use Utility\Tests\BaseTestCase as TestCase;

class AudioTest extends TestCase
{
    public function testInstance(): void
    {
        // is loadable?
        $this->assertTrue(class_exists('\Remix\Audio'));

        // is valid instance?
        \Remix\Audio::$dead = false;
        $remix = \Remix\Audio::getInstance();
        $this->assertTrue((bool)$remix);
        $this->assertTrue($remix instanceof \Remix\Audio);

        \Remix\Audio::destroy();
        \Remix\Audio::$dead = false;
    }
}
// class AudioTest
