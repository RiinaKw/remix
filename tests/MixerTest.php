<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class MixerTest extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    protected $mixer = null;

    public function __construct()
    {
        \Utility\Http\Session::start();
        parent::__construct();
    }

    protected function setUp(): void
    {
        $this->mixer = new \Remix\Instruments\Mixer();

        $tracks = [
            \Remix\Track::get('/cb', function () {
                return '<b>from callback</b>';
            })->name('testname'),
        ];
        $this->mixer->load($tracks);
    }

    public function tearDown(): void
    {
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function testRoute(): void
    {
        // is callable route?
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $response = $this->mixer->route('/cb');
        $this->assertTrue($response instanceof \Remix\Studio);
        $this->assertRegExp('/from callback/', $response->output(false));
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function test404(): void
    {
        $this->expectException(\Remix\Exceptions\HttpException::class);
        $this->expectExceptionMessage('did not match any route, given /unknwon');

        // will throw exception when unknown route?
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->mixer->route('/unknwon');
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function test405(): void
    {
        $this->expectException(\Remix\Exceptions\HttpException::class);
        $this->expectExceptionMessage('method POST not allowed, given POST /cb');

        // will throw exception when invalid method?
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->mixer->route('/cb');
    }

    public function testName(): void
    {
        $track = $this->mixer->named('testname');

        // is valid instance?
        $this->assertTrue((bool)$track);
        $this->assertTrue($track instanceof \Remix\Track);
    }

    public function testUnknownName(): void
    {
        $track = $this->mixer->named('unknwon');

        // is invalid instance?
        $this->assertNull($track);
    }
}
// class MixerTest
