<?php

namespace Remix;

use PHPUnit\Framework\TestCase;

$app_dir = __DIR__;
$root_dir = dirname($app_dir);
$autoload_path = $root_dir . '/vendor/autoload.php';

require_once($autoload_path);

class RemixTest extends TestCase
{
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
