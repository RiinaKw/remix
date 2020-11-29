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
}
