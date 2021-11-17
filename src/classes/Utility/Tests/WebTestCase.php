<?php

namespace Utility\Tests;

use PHPUnit\Framework\TestCase;

abstract class WebTestCase extends TestCase
{
    protected $daw = null;

    protected function setUp(): void
    {
        $app_dir = __DIR__ . '/../../../../demo/app';
        $this->daw = \Remix\Audio::getInstance(false)->daw->initialize($app_dir);
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    protected function request(string $path)
    {
        try {
            $_SERVER['PATH_INFO'] = $path;
            return $this->daw->playWeb();
        } catch (\Remix\Exceptions\HttpException $e) {
            $preset = \Remix\Audio::getInstance(true)->preset;

            $reverb = \Remix\Reverb::exeption($e, $preset);
            return $reverb;
        }
    }
}
