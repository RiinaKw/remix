<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class AmpTest extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    protected $amp = null;

    protected function setUp(): void
    {
        \Remix\Audio::getInstance()->daw->initializeCore();
        $this->amp = \Remix\Audio::getInstance()->amp->initialize();
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    public function testLoad(): void
    {
        // is callable with no arguments?
        $this->expectOutputRegex('/Remix framework/');
        $this->amp->play(['amp']);
    }

    public function testLoadWithParams(): void
    {
        $this->expectOutputRegex('/Remix framework/');
        $this->amp->play(['amp', 'version']);
    }
}
// class AmpTest
