<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class AmpTest extends TestCase
{
    protected $daw = null;

    protected function setUp(): void
    {
        $this->daw = \Remix\App::getInstance()->daw->initialize(__DIR__ . '/..');
    }

    public function tearDown(): void
    {
        \Remix\App::destroy();
    }

    public function testNoArg()
    {
        $this->expectOutputRegex('/Remix Amp/');

        $this->daw->playCli(['amp']);
        $this->assertTrue(\Remix\App::getInstance()->cli);
    }

    public function testInstrument()
    {
        $this->expectOutputRegex('/TB-303 and TR-808 and 909/');

        $this->daw->playCli(['amp', 'instrument:acid', '-808', '--add=909']);
    }
}
// class AmpTest
