<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class MixerTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateMethodBehavior;
    use \Remix\Utility\Tests\CaptureOutput;

    protected $bay = null;

    protected function setUp() : void
    {
        require_once(__DIR__ . '/../vendor/autoload.php');

        $remix = \Remix\App::getInstance();
        $this->mixer = $this->invokeMethod($remix, 'mixer', []);
    }

    public function testInstance()
    {
        // is valid instance?
        $this->assertTrue((bool)$this->mixer);
        $this->assertTrue($this->mixer instanceof \Remix\Mixer);
    }

    public function testLoad()
    {
        // is callable with no arguments?
        $this->startCapture();
        $this->mixer->route('');
        $result = $this->endCapture();

        $this->assertMatchesRegularExpression('/Remix Bay/', $result);
    }
}
