<?php

namespace Remix\DemoTests;

use PHPUnit\Framework\TestCase;

class AmpTest extends TestCase
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
        // is callable with no arguments?
        $this->expectOutputRegex('/Example of Effector/');
        $this->expectOutputRegex('/instrument:piano/');
        $this->expectOutputRegex('/livehouse:open/');

        $this->daw->playCli(['amp']);
    }

    public function testInstrument(): void
    {
        // is callable with no arguments?
        $this->expectOutputRegex('/I am Instrument belonging to Audio, which instruments do you like\?/');

        $this->daw->playCli(['amp', 'instrument']);
    }
}
