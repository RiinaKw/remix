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
    }

    /**
     * @runInSeparateProcess
     */
    public function testVader(): void
    {
        $this->request('/vader');
        $this->assertHtmlContains('Luke, I am your father');
    }

    /**
     * @runInSeparateProcess
     */
    public function testRiina(): void
    {
        $this->request('/vader/riina');
        $this->assertHtmlContains('riina, I am your father');
    }

    /**
     * @runInSeparateProcess
     */
    public function test404(): void
    {
        $this->request('/noexists');
        $this->assertHtmlContains('That\'s 404');
    }
}
