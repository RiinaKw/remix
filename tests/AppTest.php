<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    public function testInstance(): void
    {
        // is loadable?
        $this->assertTrue(class_exists('\Remix\Audio'));

        // is valid instance?
        $remix = \Remix\Audio::getInstance();
        $this->assertTrue((bool)$remix);
        $this->assertTrue($remix instanceof \Remix\Audio);

        \Remix\Audio::destroy();
    }
}
// class AppTest
