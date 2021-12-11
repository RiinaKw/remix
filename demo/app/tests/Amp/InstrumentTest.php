<?php

namespace Remix\DemoTests;

use Remix\Demo\TestCase\CliTestCase as TestCase;

class InstrumentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Be sure to point to the app directory
        $this->initialize(__DIR__ . '/../..');
    }

    public function testNoArgs(): void
    {
        $this->expectOutputRegex('/I am Instrument belonging to Audio, which instruments do you like\?/');

        $this->execute('amp instrument');
    }

    public function testPiano(): void
    {
        $this->expectOutputRegex('/4\'33\"/');

        $this->execute('amp instrument:piano');
    }

    public function testGuitar(): void
    {
        $this->expectOutputRegex('/SMOKE ON THE WATER/');

        $this->execute('amp instrument:guitar');
    }

    public function testAcid(): void
    {
        $this->expectOutputRegex('/TB-303/');

        $this->execute('amp instrument:acid');
    }

    public function testAcidWith808(): void
    {
        $this->expectOutputRegex('/TB-303/');
        $this->expectOutputRegex('/TR-808/');

        $this->execute('amp instrument:acid -808');
    }

    public function testAcidWith909(): void
    {
        $this->expectOutputRegex('/TB-303/');
        $this->expectOutputRegex('/TR-808/');
        $this->expectOutputRegex('/TR-909/');

        $this->execute('amp instrument:acid -808 --add=TR-909');
    }
}
