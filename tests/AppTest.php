<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    protected function setUp() : void
    {
        require_once(__DIR__ . '/../vendor/autoload.php');
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
}
