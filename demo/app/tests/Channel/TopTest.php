<?php

namespace Remix\DemoTests;

use Remix\Lyric;
use Utility\Tests\WebTestCase;

class TopTest extends WebTestCase
{
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
    public function test404(): void
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
    public function test302(): void
    {
        $this->get('/302');
        $this->assertStatusCode(302);
        $this->assertRedirectUri(Lyric::getInstance()->make('/redirected'));
    }
}
