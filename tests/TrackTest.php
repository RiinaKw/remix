<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class TrackTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    public function testLoad() : void
    {
        $track = \Remix\Track::get('/:test', 'SampleTrack@index');
        $this->assertNotNull($track);
    }

    public function testMatch() : void
    {
        // is match?
        $track = \Remix\Track::get('/user/:id', 'SampleTrack@index');
        $match = $track->isMatch('/user/1');
        $this->assertSame(true, $match);
        $this->assertSame(['id' => '1'], $track->matched());

        // is not match?
        $track = \Remix\Track::get('/diary/:year/:month', 'SampleTrack@index');
        $match = $track->isMatch('/boo');
        $this->assertSame(false, $match);
    }
}
