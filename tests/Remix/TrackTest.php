<?php

namespace Remix\CoreTests;

use Utility\Tests\BaseTestCase as TestCase;
// Target of the test
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
