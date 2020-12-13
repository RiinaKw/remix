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
        $this->daw = \Remix\Audio::getInstance()->initialize()->daw->initialize(__DIR__ . '/..');
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
        $this->assertMatchesRegularExpression('/Remix is ​​a lightweight PHP framework./', (string)$response);

        // callback
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/cb';
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertMatchesRegularExpression('/from callback/', (string)$response);
/*
        // bounce
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/bounce';
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertMatchesRegularExpression('/hello/', (string)$response);

        // bounce with parameters
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/bounce/heydj';
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertMatchesRegularExpression('/heydj/', (string)$response);
*/
        // redirect
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/redirect';
        $response = $this->daw->playWeb($this->public_dir);
        $status = $this->invokeProperty($response, 'status');
        $this->assertSame(303, $status);
/*
        // post
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['PATH_INFO'] = '/form/dummy_id';
        $_POST['title'] = 'dummy_title';
        $response = $this->daw->playWeb($this->public_dir);
        $this->assertMatchesRegularExpression('/dummy_id/', (string)$response);
        $this->assertMatchesRegularExpression('/dummy_title/', (string)$response);

        // method not allowed
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['PATH_INFO'] = '/postonly';
        $response = $this->daw->playWeb($this->public_dir);
        $status = $this->invokeProperty($response, 'status');
        $this->assertSame(405, $status);
        $this->assertMatchesRegularExpression('/method not allowed/', (string)$response);
*/
    }
}
// class MixerTest
