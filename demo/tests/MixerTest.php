<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class MixerTest extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    protected $daw = null;

    protected function setUp(): void
    {
        $this->daw = \Remix\Audio::getInstance()->initialize()->daw->initialize(__DIR__ . '/../app');
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testMixer()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/';
        $response = $this->daw->playWeb();
        $this->assertFalse(\Remix\Audio::getInstance()->cli);
        $this->assertRegExp('/Remix is ​​a lightweight PHP framework./', $response->output(false));

        // callback
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/cb';
        $response = $this->daw->playWeb();
        $this->assertRegExp('/from callback/', $response->output(false));

        // bounce
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/bounce';
        $response = $this->daw->playWeb();
        $this->assertRegExp('/hello/', $response->output(false));

        // bounce with parameters
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/bounce/heydj';
        $response = $this->daw->playWeb();
        $this->assertRegExp('/heydj/', $response->output(false));

        // redirect
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/redirect';
        $response = $this->daw->playWeb();
        $this->assertSame(303, $response->getStatusCode());

        // post
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['PATH_INFO'] = '/form/dummy_id';
        $_POST['title'] = 'dummy_title';
        $response = $this->daw->playWeb();
        $this->assertRegExp('/dummy_id/', $response->output(false));
        $this->assertRegExp('/dummy_title/', $response->output(false));

        // method not allowed
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/postonly';
        $response = $this->daw->playWeb();
        $this->assertSame(405, $response->getStatusCode());
        $this->assertRegExp('/not allowed/', $response->output(false));
    }
}
// class MixerTest
