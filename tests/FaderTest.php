<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class FaderTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;

    public function testLoad() : void
    {
        $fader = \Remix\Fader::factory('');
        $this->assertNotNull($fader);
    }

    public function testTranslate() : void
    {
        $translated = $this->invokeStaticMethod(\Remix\Fader::class, 'translate', ['/:test']);
        $this->assertSame('/^\/(?<test>\S+?)\\/?$/', $translated);

        $translated = $this->invokeStaticMethod(\Remix\Fader::class, 'translate', ['effector :param1 :param2']);
        $this->assertSame('/^effector (?<param1>\S+?) (?<param2>\S+?)\/?$/', $translated);
    }

    public function testMatch() : void
    {
        // is match?
        $fader = \Remix\Fader::factory('/:test');
        $match = $fader->isMatch('/aaa');
        $this->assertSame(true, $match);
        $this->assertSame(['test' => 'aaa'], $fader->matched());

        // is multiple matches?
        $fader = \Remix\Fader::factory('effector :param1 :param2');
        $match = $fader->isMatch('effector id name');
        $this->assertSame(true, $match);
        $this->assertSame(['param1' => 'id', 'param2' => 'name'], $fader->matched());

        // is non-ASCII characters?
        $fader = \Remix\Fader::factory('effector :file');
        $match = $fader->isMatch('effector test.txt');
        $this->assertSame(true, $match);
        $this->assertSame(['file' => 'test.txt'], $fader->matched());

        // is non-parameter match?
        $fader = \Remix\Fader::factory('/test');
        $match = $fader->isMatch('/test');
        $this->assertSame(true, $match);
        $this->assertSame([], $fader->matched());

        // is not match?
        $fader = \Remix\Fader::factory('/test');
        $match = $fader->isMatch('/boo');
        $this->assertSame(false, $match);
    }
}
