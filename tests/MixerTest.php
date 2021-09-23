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
        $this->mixer = \Remix\Audio::getInstance()->mixer;

        $tracks = [
            \Remix\Track::get('/cb', function () {
                return '<b>from callback</b>';
            })->name('testname'),
        ];
        $this->mixer->load($tracks);
    }

    public function tearDown(): void
    {
        \Remix\Audio::destroy();
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
        // will throw exception when unknown route?
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $response = $this->mixer->route('/unknwon');
        $this->assertSame(404, $response->getStatusCode());
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
