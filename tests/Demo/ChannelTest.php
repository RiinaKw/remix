<?php

namespace Remix\DemoTests;

use Utility\Tests\WebTestCase;

class ChannelTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->initialize(__DIR__ . '/../../demo/app');
    }

    /**
     * @runInSeparateProcess
     */
    public function testTop(): void
    {
        $this->request('/');
        $this->assertHtmlContains('I am your Remix.');
        $this->assertStatusCode(200);
        $this->assertMimeType('text/plain');
    }

    /**
     * @runInSeparateProcess
     */
    public function testVader(): void
    {
        $this->request('/vader');
        $this->assertHtmlContains('Luke, I am your father');
        $this->assertStatusCode(200);
        $this->assertMimeType('text/html');
    }

    /**
     * @runInSeparateProcess
     */
    public function testRiina(): void
    {
        $this->request('/vader/riina');
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
        $this->request('/noexists');
        $this->assertHtmlContains('That\'s 404');
        $this->assertStatusCode(404);
        $this->assertMimeType('text/html');
    }
}
