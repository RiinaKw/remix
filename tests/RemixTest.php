<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class RemixTest extends TestCase
{
    protected function setUp() : void
    {
        require_once(__DIR__ . '/../vendor/autoload.php');
    }

    public function testRemixInstance()
    {
        // is loadable?
        $this->assertTrue(class_exists('\Remix\App'));

        // is valid instance?
        $remix = \Remix\App::getInstance();
        $this->assertTrue((bool)$remix);
        $this->assertTrue($remix instanceof \Remix\App);
    }

    public function testRemixLoad()
    {
        $root_path = __DIR__ . '/../demo';

        $remix = \Remix\App::getInstance();
        $remix->initialize($root_path);

        // is valid app dir?
        $this->assertEquals(realpath($root_path . '/app'), $remix->appDir());
    }
}
