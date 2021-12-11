<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\Track;

class TrackTest extends TestCase
{
    public function testLoad(): void
    {
        $track = Track::get('/:test', 'SampleTrack@index');
        $this->assertNotNull($track);
    }
}
// class TrackTest
