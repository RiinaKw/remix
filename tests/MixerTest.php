<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class MixerTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    protected $mixer = null;

    protected function setUp(): void
    {
        $remix = \Remix\App::getInstance();
        $this->mixer = $this->invokeMethod($remix, 'mixer', []);

        $tracks = [
            \Remix\Track::get('/cb', function () {
                return '<b>from callback</b>';
            })->name('testname'),
        ];
        $this->mixer->load($tracks);
    }

    public function tearDown(): void
    {
        \Remix\App::destroy();
    }

    public function testRoute(): void
    {
        // is callable route?
        $response = $this->mixer->route('/cb');
        $this->assertTrue($response instanceof \Remix\Studio);
        $this->assertMatchesRegularExpression('/from callback/', $response);
    }

    public function test404(): void
    {
        $this->expectException(\Remix\Exceptions\HttpException::class);

        // will throw exception when unknown route?
        $response = $this->mixer->route('/unknwon');
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
