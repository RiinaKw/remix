<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class AmpTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    protected $amp = null;

    protected function setUp() : void
    {
        $remix = \Remix\App::getInstance();
        $this->amp = $this->invokeMethod($remix, 'amp', []);
    }

    public function tearDown() : void
    {
        \Remix\App::destroy();
    }

    public function testLoad() : void
    {
        // is callable with no arguments?
        $this->expectOutputRegex('/Remix Amp/');
        $this->amp->run(['amp']);
    }

    public function testLoadWithParams() : void
    {
        $this->expectOutputRegex('/Remix framework/');
        $this->amp->run(['amp', 'version']);
    }
}
