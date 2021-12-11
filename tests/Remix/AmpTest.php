<?php

namespace Remix\CoreTests;

use Utility\Tests\BaseTestCase as TestCase;
// Target of the test
use Remix\Instruments\Amp;
// Remix core
use Remix\Instruments\DAW;
use Remix\Audio;

class AmpTest extends TestCase
{
    protected $amp = null;

    protected function setUp(): void
    {
        $daw = (new DAW())->initializeCore();
        $this->amp = (new Amp())->initialize($daw);
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
