<?php

namespace Remix\CoreTests;

use Utility\Tests\BaseTestCase as TestCase;
// Target of the test
use Remix\Instruments\Amp;
// Remix core
use Remix\Instruments\DAW;
use Remix\Audio;
// Utility
use Utility\Reflection\ReflectionObject;

class AmpTest extends TestCase
{
    protected $amp = null;

    protected function setUp(): void
    {
        $audio = Audio::getInstance();

        $this->amp = new Amp();
        (new ReflectionObject($this->amp))->setProp('audio', $audio);

        $daw = $audio->daw->initializeCore();
        $this->amp->initialize($daw);
    }

    public function tearDown(): void
    {
        Audio::destroy();
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
