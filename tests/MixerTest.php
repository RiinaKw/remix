<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class MixerTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateMethodBehavior;
    use \Remix\Utility\Tests\CaptureOutput;

    protected $bay = null;

    protected function setUp() : void
    {
        require_once(__DIR__ . '/../vendor/autoload.php');

        $remix = \Remix\App::getInstance();
        $this->mixer = $this->invokeMethod($remix, 'mixer', []);

        $tracks = [
            \Remix\Track::get('/cb', function () {
                return '<b>from callback</b>';
            })->name('testname'),
        ];
        $this->mixer->load($tracks);
    }

    public function testRoute()
    {
        // is callable route?
        $response = $this->mixer->route('/cb');
        $this->assertMatchesRegularExpression('/from callback/', $response);
    }

    public function test404()
    {
        $this->expectException(\Remix\Exceptions\HttpException::class);

        // will throw exception when unknown route?
        $response = $this->mixer->route('/unknwon');
    }

    public function testName()
    {
        $track = $this->mixer->named('testname');

        // is valid instance?
        $this->assertTrue((bool)$track);
        $this->assertTrue($track instanceof \Remix\Track);
    }

    public function testUnknownName()
    {
        $track = $this->mixer->named('unknwon');

        // is invalid instance?
        $this->assertNull($track);
    }
}
