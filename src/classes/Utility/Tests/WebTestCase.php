<?php

namespace Utility\Tests;

use PHPUnit\Framework\TestCase;

abstract class WebTestCase extends TestCase
{
    protected $daw = null;

    protected function setUp(): void
    {
        $app_dir = __DIR__ . '/../../../../demo/app';
        $this->daw = (new \Remix\Instruments\DAW())->initialize($app_dir);
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    protected function request(string $path)
    {
        $_SERVER['PATH_INFO'] = $path;
        return $this->daw->playWeb();
    }
}
