<?php

namespace Utility\Tests;

use Utility\Tests\DemoTestCase;

abstract class WebTestCase extends DemoTestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    protected $studio = null;
    protected $html = null;

    private function request(string $path)
    {
        try {
            $_SERVER['PATH_INFO'] = $path;
            $this->daw->playWeb();
            $reverb = $this->invokeProperty($this->daw, 'reverb');
        } catch (\Remix\Exceptions\HttpException $e) {
            $reverb = \Remix\Reverb::exeption($e, \Remix\Audio::getInstance(true)->preset);
        }
        $this->studio = $this->invokeProperty($reverb, 'studio');
        $this->html = $this->studio->output(false);
    }

    protected function get(string $path)
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->request($path);
    }

    protected function post(string $path, array $post = [])
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = $post;
        $this->request($path);
    }

    protected function assertHtmlContains(string $text)
    {
        $this->assertNotFalse(strpos($this->html, $text), "The HTML does not contain '{$text}'");
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
