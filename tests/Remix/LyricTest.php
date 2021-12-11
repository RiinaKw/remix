<?php

namespace Remix\CoreTests;

use Utility\Tests\BaseTestCase as TestCase;
// Target of the test
use Remix\Lyric;
// Remix core
use Remix\Audio;
use Remix\Track;

class LyricTest extends TestCase
{
    protected $lyric = null;

    protected function setUp(): void
    {
        Audio::destroy();
        $audio = Audio::getInstance();

        $tracks = [
            Track::get('/cb', function () {
                return '<b>from callback</b>';
            })->name('testname'),
            Track::get('/vader/:name', 'TopChannel@vader')->name('father'),
        ];
        $audio->mixer->load($tracks);

        $audio->preset->set('app.public_uri', 'http://remix.example.com/framework/');

        $this->lyric = Lyric::getInstance();
    }

    public function testPath(): void
    {
        $this->assertSame(
            'http://remix.example.com/framework/user/1',
            $this->lyric->sing('/user/1')
        );
    }

    public function testName(): void
    {
        $this->assertSame(
            'http://remix.example.com/framework/cb',
            $this->lyric->named('testname')
        );
    }

    public function testNameWithParams(): void
    {
        $this->assertSame(
            'http://remix.example.com/framework/vader/riina',
            $this->lyric->named('father', [':name' => 'riina'])
        );
    }
}
