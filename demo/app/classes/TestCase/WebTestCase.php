<?php

namespace Remix\Demo\TestCase;

// Remix core
use Remix\Audio;
use Remix\Reverb;
// Utility
use Utility\Reflection\ReflectionObject;
// Exception
use Remix\Exceptions\HttpException;

/**
 * PHPUnit TestCase base class for web (DAW) in demo environment
 *
 * @package  TestCase\Demo
 */
abstract class WebTestCase extends DemoTestCase
{
    protected $studio = null;
    protected $html = null;

    protected $prev_path = '';
    protected $prev_method = '';
    protected $prev_post = [];

    protected function initialize(string $app_dir)
    {
        // Turn off the CLI flag
        $reflection = new ReflectionObject(Audio::getInstance());
        $reflection->setProp('is_cli', false);

        // Initialize for web operation
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
        // Back up properties
        $this->prev_path = $path;
        $this->prev_method = $this->METHOD;
        $this->prev_post = $this->POST;
        try {
            // Do it
            $this->PATH = $path;
            $this->daw->playWeb();

            // Get protected $reberve from DAW
            $reflection = new ReflectionObject($this->daw);
            $reverb = $reflection->getProp('reverb');
        } catch (HttpException $e) {
            $reverb = Reverb::exeption($e, Audio::getInstance()->preset);
        }

        // Get protected $studio from Reverb
        $reflection = new ReflectionObject($reverb);
        $this->studio = $reflection->getProp('studio');

        $this->html = $this->studio->recorded();
        $this->studio->sendHeader();
    }

    protected function reload()
    {
        // Restore properties
        $this->METHOD = $this->prev_method;
        $this->POST = $this->prev_post;

        // Do it
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
}
