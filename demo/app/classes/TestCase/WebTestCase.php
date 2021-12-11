<?php

namespace RemixDemo\TestCase;

// Remix core
use Remix\Audio;
use Remix\Reverb;
use Remix\Tuners\Cli as CliTuner;
// Utility
use Utility\Reflection\ReflectionObject;
// Exception
use Remix\Exceptions\HttpException;

/**
 * PHPUnit TestCase base class for web (DAW) in demo environment.
 *
 * @package  TestCase\Demo
 */
abstract class WebTestCase extends DemoTestCase
{
    /**
     * @property \Remix\Instruments\DAW $remixDaw
     */

    /**
     * Stuido instance
     * @var \Remix\Studio
     */
    protected $remixStudio = null;

    /**
     * HTML string of the response
     * @var string
     */
    protected $html = null;

    /**
     * The path from the last access
     * @var string
     */
    protected $prev_path = '';

    /**
     * The method from the last access
     * @var string
     */
    protected $prev_method = '';

    /**
     * The $_POST from the last access
     * @var array<string, mixed>
     */
    protected $prev_post = [];

    /**
     * Initialize application, turn off CLI flag.
     */
    protected function setUp(): void
    {
        // Turn off the CLI flag
        $reflection = new ReflectionObject(Audio::getInstance());
        $reflection->setProp('tunerCli', new CliTuner('web'));

        // Complete the DAW settings first
        parent::setUp();
    }

    /**
     * Get super global variables.
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
     * Set super global variables.
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

    /**
     * Send a web request.
     * @param  string $path  Path to access
     */
    private function request(string $path): void
    {
        // Back up properties
        $this->prev_path = $path;
        $this->prev_method = $this->METHOD;
        $this->prev_post = $this->POST;
        try {
            // Do it
            $this->PATH = $path;
            $this->remixDaw->playWeb();

            // Get protected $reberve from DAW
            $reflection = new ReflectionObject($this->remixDaw);
            $reverb = $reflection->getProp('reverb');
        } catch (HttpException $e) {
            $reverb = Reverb::exeption($e, Audio::getInstance()->preset);
        }

        // Get protected $studio from Reverb
        $reflection = new ReflectionObject($reverb);
        $this->remixStudio = $reflection->getProp('studio');

        $this->html = $this->remixStudio->recorded();
        $this->remixStudio->sendHeader();
    }

    /**
     * Reload the current request.
     */
    protected function reload(): void
    {
        // Restore properties
        $this->METHOD = $this->prev_method;
        $this->POST = $this->prev_post;

        // Do it
        $this->request($this->prev_path);
    }

    /**
     * Send a web request with GET method.
     * @param  string $path  Path to access
     */
    protected function get(string $path): void
    {
        $this->METHOD = 'GET';
        $this->request($path);
    }

    /**
     * Send a web request with POST method.
     * @param  string $path                 Path to access
     * @param  array<string, mixed>  $post  Array of $_POST
     */
    protected function post(string $path, array $post = []): void
    {
        $this->METHOD = 'POST';
        $this->POST = $post;
        $this->request($path);
    }

    /**
     * Does the HTML contain the specified string?
     * @param string $string  string that must be included
     */
    protected function assertHtmlContains(string $string): void
    {
        $this->assertNotFalse(
            strpos($this->html, $string),
            "The HTML does not contain '{$string}'"
        );
    }

    /**
     * Do the status code of the response match?
     * @param int $code  Status code that must match
     */
    protected function assertStatusCode(int $code): void
    {
        $this->assertSame($code, $this->remixStudio->getStatusCode());
    }

    /**
     * Do the mime type of the response match?
     * @param string $mimetype  Mime type that must match
     */
    protected function assertMimeType(string $mimetype): void
    {
        $this->assertSame($mimetype, $this->remixStudio->getMimeType());
    }
}
