<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;
use Remix\Fader;

class FaderTest extends TestCase
{
    use \Utility\Tests\InvokePrivateBehavior;

    public function testLoad(): void
    {
        $fader = new Fader('');
        $this->assertNotNull($fader);
    }

    public function testTranslate(): void
    {
        $translated = $this->invokeStaticMethod(Fader::class, 'translate', ['/:test']);
        $this->assertSame('/^\/(?<test>\S+?)\\/?$/', $translated);

        $translated = $this->invokeStaticMethod(Fader::class, 'translate', ['effector :param1 :param2']);
        $this->assertSame('/^effector (?<param1>\S+?) (?<param2>\S+?)\/?$/', $translated);
    }

    public function testMatch(): void
    {
        // is match?
        $fader = new Fader('/:test');
        $match = $fader->isMatch('/aaa');
        $this->assertSame(true, $match);
        $this->assertSame(['test' => 'aaa'], $fader->matched());

        // is multiple matches?
        $fader = new Fader('effector :param1 :param2');
        $match = $fader->isMatch('effector id name');
        $this->assertSame(true, $match);
        $this->assertSame(['param1' => 'id', 'param2' => 'name'], $fader->matched());

        // is non-ASCII characters?
        $fader = new Fader('effector :file');
        $match = $fader->isMatch('effector test.txt');
        $this->assertSame(true, $match);
        $this->assertSame(['file' => 'test.txt'], $fader->matched());

        // is non-parameter match?
        $fader = new Fader('/test');
        $match = $fader->isMatch('/test');
        $this->assertSame(true, $match);
        $this->assertSame([], $fader->matched());

        // is not match?
        $fader = new Fader('/test');
        $match = $fader->isMatch('/boo');
        $this->assertSame(false, $match);
    }
}
// class FaderTest
