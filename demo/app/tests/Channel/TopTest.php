<?php

namespace RemixDemo\Tests;

use RemixDemo\TestCase\WebTestCase as TestCase;
// Traits
use Utility\Tests\Traits;

/**
 * Test of TopChannel in the demo env.
 * @package  Demo\TestCase\Channels
 */
class TopTest extends TestCase
{
    use Traits\Redirect;

    protected function setUp(): void
    {
        parent::setUp();

        // Be sure to point to the app directory
        $this->initialize(__DIR__ . '/../..');
    }

    /**
     * @runInSeparateProcess
     */
    public function testTop(): void
    {
        $this->get('/');
        $this->assertHtmlContains('I am your Remix.');
        $this->assertStatusCode(200);
        $this->assertMimeType('text/plain');
    }

    /**
     * @runInSeparateProcess
     */
    public function testVader(): void
    {
        $this->get('/vader');
        $this->assertHtmlContains('Luke, I am your father');
        $this->assertStatusCode(200);
        $this->assertMimeType('text/html');
    }

    /**
     * @runInSeparateProcess
     */
    public function testRiina(): void
    {
        $this->get('/vader/riina');
        $this->assertHtmlContains('riina, I am your father');
        $this->assertStatusCode(200);
        $this->assertMimeType('text/html');
    }

    /**
     * @runInSeparateProcess
     */
    public function testStatus404(): void
    {
        // Note that if you get an HttpException, you are getting the error page for App
        $this->get('/noexists');
        $this->assertHtmlContains('That\'s 404');
        $this->assertStatusCode(404);
        $this->assertMimeType('text/html');
    }

    /**
     * @runInSeparateProcess
     */
    public function testStatus302(): void
    {
        $this->get('/302');
        $this->assertStatusCode(302);
        $this->assertRedirectName('redirected');
    }
}
