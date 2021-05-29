<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class AmpTest extends TestCase
{
    protected $daw = null;

    protected function setUp(): void
    {
        $this->daw = \Remix\Audio::getInstance()->daw->initialize(__DIR__ . '/../app');
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    public function testNoArg()
    {
        $this->expectOutputRegex('/Remix framework/');

        $this->daw->playCli(['amp']);
        $this->assertTrue(\Remix\Audio::getInstance()->cli);
    }

    public function testInstrument()
    {
        $this->expectOutputRegex('/TB-303(.*?) and (.*?)TR-808(.*?) and (.*?)TB-909/');

        $this->daw->playCli(['amp', 'instrument:acid', '-808', '--add=TB-909']);
    }

    public function testInvalidArg()
    {
        $this->expectOutputRegex('/unknown effector/');

        $this->daw->playCli(['amp', 'boo']);
        $this->assertTrue(\Remix\Audio::getInstance()->cli);
    }
}
// class AmpTest
