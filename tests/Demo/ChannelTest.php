<?php

namespace Remix\DemoTests;

use Utility\Tests\WebTestCase;

class ChannelTest extends WebTestCase
{

    /**
     * @runInSeparateProcess
     */
    public function testTop(): void
    {
        $html = (string)$this->request('/');
        $this->assertRegExp('/I am your Remix/', $html);
    }

    /**
     * @runInSeparateProcess
     */
    public function testVader(): void
    {
        $html = (string)$this->request('/vader');
        $this->assertRegExp('/Luke, I am your father/', $html);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRiina(): void
    {
        $html = (string)$this->request('/vader/riina');
        $this->assertRegExp('/riina, I am your father/', $html);
    }

    /**
     * @runInSeparateProcess
     */
    public function test404(): void
    {
        $html = (string)$this->request('/noexists');
        $this->assertRegExp('/That\'s 404/', $html);
    }
}
