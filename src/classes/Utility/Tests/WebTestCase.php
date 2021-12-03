<?php

namespace Utility\Tests;

use Utility\Tests\DemoTestCase;
use Remix\Audio;
use Remix\Reverb;
use Remix\Exceptions\HttpException;

abstract class WebTestCase extends DemoTestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    protected $studio = null;
    protected $html = null;

    protected $prev_path = '';
    protected $prev_method = '';
    protected $prev_post = [];

    protected function initialize(string $app_dir)
    {
        Audio::isDebug();

        // Turn off the CLI flag
        $this->invokePropertyValue(Audio::getInstance(), 'is_cli', false);

        $this->daw->initialize($app_dir);
        chdir($app_dir . '/..');
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __get(string $key)
    {
        switch ($key) {
            case 'METHOD':
                return $_SERVER['REQUEST_METHOD'];
            case 'POST':
                return $_POST;
        }
        return null;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __set(string $key, $value)
    {
        switch ($key) {
            case 'PATH':
                $_SERVER['PATH_INFO'] = $value;
                break;
            case 'METHOD':
                $_SERVER['REQUEST_METHOD'] = $value;
                break;
            case 'POST':
                $_POST = $value;
                break;
        }
    }

    private function request(string $path)
    {
        $this->prev_path = $path;
        $this->prev_method = $this->METHOD;
        $this->prev_post = $this->POST;
        try {
            $this->PATH = $path;
            $this->daw->playWeb();
            $reverb = $this->invokeProperty($this->daw, 'reverb');
        } catch (HttpException $e) {
            $reverb = Reverb::exeption($e, Audio::getInstance()->preset);
        }
        $this->studio = $this->invokeProperty($reverb, 'studio');
        $this->html = $this->studio->output(false);
    }

    protected function reload()
    {
        $this->METHOD = $this->prev_method;
        $this->POST = $this->prev_post;
        $this->request($this->prev_path);
    }

    protected function get(string $path)
    {
        $this->METHOD = 'GET';
        $this->request($path);
    }

    protected function post(string $path, array $post = [])
    {
        $this->METHOD = 'POST';
        $this->POST = $post;
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
