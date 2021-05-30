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
            $response->recorded();
            $this->assertSame(200, $response->getStatusCode());
        }
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testMimeType()
    {
        $_SERVER['PATH_INFO'] = '/sample/text';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $response = $this->daw->playWeb();
        $response->recorded();
        $this->assertSame('text/plain', $response->getMimeType());

        $_SERVER['PATH_INFO'] = '/sample/xml';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $response = $this->daw->playWeb();
        $response->recorded();
        $this->assertSame('application/xml', $response->getMimeType());

        $_SERVER['PATH_INFO'] = '/sample/json';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $response = $this->daw->playWeb();
        $response->recorded();
        $this->assertSame('application/json', $response->getMimeType());
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testStatus()
    {
        $_SERVER['PATH_INFO'] = '/sample/status';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $response = $this->daw->playWeb();
        $response->recorded();
        $this->assertSame(500, $response->getStatusCode());

        $_SERVER['PATH_INFO'] = '/sample/status/418';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $response = $this->daw->playWeb();
        $response->recorded();
        $this->assertSame(418, $response->getStatusCode());
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
            $this->assertSame(500, $e->getStatusCode());
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
            $this->assertSame(418, $e->getStatusCode());
        }
    }
}
// class testChannel
