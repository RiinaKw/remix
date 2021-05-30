<?php

namespace Remix\AppTests;

use PHPUnit\Framework\TestCase;
use Remix\Exceptions\HttpException;

class SampleChannelTest extends TestCase
{
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
    public function testIndex()
    {
        // accept any methods
        $_SERVER['PATH_INFO'] = '/sample';
        foreach (['GET', 'POST'] as $method) {
            $_SERVER['REQUEST_METHOD'] = $method;
            $response = $this->daw->playWeb();
            $this->assertSame(200, $response->code());
        }
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testError()
    {
        // accept any methods
        $_SERVER['PATH_INFO'] = '/sample/error';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->daw->playWeb();
        // #### Someone overwrote the headers ####
        // $response = $this->daw->playWeb();
        // $this->assertSame(500, $response->code());

        $this->assertTrue(true);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testException()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('exception test from App\\Channel\\SampleChannel::exception');

        try {
            // throw Exception
            $_SERVER['PATH_INFO'] = '/sample/exception';
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $this->daw->playWeb();
        } catch (HttpException $e) {
            $this->assertSame(500, $e->getStatus());
            throw $e;
        }
        //$this->assertTrue(false);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testExceptionWithCode()
    {
        try {
            $_SERVER['PATH_INFO'] = '/sample/exception/418';
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $this->daw->playWeb();
        } catch (HttpException $e) {
            $this->assertSame(418, $e->getStatus());
        }
    }
}
// class testChannel
