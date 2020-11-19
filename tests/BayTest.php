<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class BayTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    protected $bay = null;

    protected function setUp() : void
    {
        $remix = \Remix\App::getInstance();
        $this->bay = $this->invokeMethod($remix, 'bay', []);
    }

    public function tearDown() : void
    {
        \Remix\App::destroy();
    }

    public function testLoad()
    {
        // is callable with no arguments?
        $this->expectOutputRegex('/Remix Bay/');
        $this->bay->run(['bay']);
    }

    public function testLoadWithParams()
    {
        $this->expectOutputRegex('/Remix framework/');
        $this->bay->run(['bay', 'version']);
    }
}
