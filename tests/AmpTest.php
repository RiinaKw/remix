<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class AmpTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    protected $amp = null;

    protected function setUp(): void
    {
        $this->amp = \Remix\App::getInstance()->amp;
    }

    public function tearDown(): void
    {
        \Remix\App::destroy();
    }

    public function testLoad(): void
    {
        // is callable with no arguments?
        $this->expectOutputRegex('/Remix Amp/');
        $this->amp->play(['amp']);
    }

    public function testLoadWithParams(): void
    {
        $this->expectOutputRegex('/Remix framework/');
        $this->amp->play(['amp', 'version']);
    }
}
// class AmpTest
