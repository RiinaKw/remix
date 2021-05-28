<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;

class MixerTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    protected $daw = null;
    protected $public_dir = __DIR__ . '../public';

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
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertFalse(\Remix\Audio::getInstance()->cli);
        $this->assertRegExp('/Remix is ​​a lightweight PHP framework./', $response->output(false));

        // callback
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/cb';
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertRegExp('/from callback/', $response->output(false));

        // bounce
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/bounce';
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertRegExp('/hello/', $response->output(false));

        // bounce with parameters
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/bounce/heydj';
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertRegExp('/heydj/', $response->output(false));

        // redirect
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/redirect';
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertSame(303, $response->code());

        // post
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['PATH_INFO'] = '/form/dummy_id';
        $_POST['title'] = 'dummy_title';
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertRegExp('/dummy_id/', $response->output(false));
        $this->assertRegExp('/dummy_title/', $response->output(false));

        // method not allowed
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/postonly';
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertSame(405, $response->code());
        $this->assertRegExp('/method not allowed/', $response->output(false));
    }
}
// class MixerTest
