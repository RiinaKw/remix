<?php

namespace Remix\DemoTests;

use PHPUnit\Framework\TestCase;

class InstrumentTest extends TestCase
{
    private $daw;

    protected function setUp(): void
    {
        $demo_dir = realpath(__DIR__ . '/../../../demo');
        chdir($demo_dir);
        var_dump($demo_dir);
        $this->daw = \Remix\Audio::getInstance(false)->daw;
        $this->initialize($demo_dir . '/app');
    }

    protected function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    protected function initialize(string $app_dir)
    {
        $this->daw->initialize($app_dir);
    }

    public function testNoArg(): void
    {
        $this->expectOutputRegex('/I am Instrument belonging to Audio, which instruments do you like\?/');

        $this->daw->playCli(['amp', 'instrument']);
    }

    public function testPiano(): void
    {
        $this->expectOutputRegex('/4\'33\"/');

        $this->daw->playCli(['amp', 'instrument:piano']);
    }

    public function testGuitar(): void
    {
        $this->expectOutputRegex('/SMOKE ON THE WATER/');

        $this->daw->playCli(['amp', 'instrument:guitar']);
    }

    public function testAcid(): void
    {
        $this->expectOutputRegex('/TB-303/');

        $this->daw->playCli(['amp', 'instrument:acid']);
    }

    public function testAcid808(): void
    {
        $this->expectOutputRegex('/TB-303/');
        $this->expectOutputRegex('/TR-808/');

        $this->daw->playCli(['amp', 'instrument:acid', '-808']);
    }

    public function testAcid909(): void
    {
        $this->expectOutputRegex('/TB-303/');
        $this->expectOutputRegex('/TR-808/');
        $this->expectOutputRegex('/TR-909/');

        $this->daw->playCli(['amp', 'instrument:acid', '-808', '--add=TR-909']);
    }
}
