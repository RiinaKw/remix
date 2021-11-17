<?php

namespace Utility\Tests;

use PHPUnit\Framework\TestCase;

abstract class WebTestCase extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    protected $daw = null;
    protected $studio = null;
    //protected $reverb = null;
    protected $html = null;

    protected function setUp(): void
    {
        $this->daw = \Remix\Audio::getInstance(false)->daw;
    }

    protected function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    protected function initialize(string $app_dir)
    {
        $this->daw->initialize($app_dir);
    }

    protected function request(string $path)
    {
        try {
            $_SERVER['PATH_INFO'] = $path;
            $reverb = $this->daw->playWeb();
        } catch (\Remix\Exceptions\HttpException $e) {
            $preset = \Remix\Audio::getInstance(true)->preset;

            $reverb = \Remix\Reverb::exeption($e, $preset);
        }
        $this->studio = $this->invokeProperty($reverb, 'studio');
        $this->html = (string)$reverb;
    }

    protected function assertHtmlContains(string $text)
    {
        $this->assertTrue(strpos($this->html, $text) !== false);
    }

    protected function assertStatusCode(int $code)
    {
        $this->assertSame($code, $this->studio->getStatusCode());
    }

    protected function assertMimeType(string $mime)
    {
        $this->assertSame($mime, $this->studio->getMimeType());
    }

    protected function assertRedirectUri(string $uri)
    {
        $this->assertSame($uri, $this->studio->getRedirectUri());
    }
}
