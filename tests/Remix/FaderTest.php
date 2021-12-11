<?php

namespace Remix\CoreTests;

use Utility\Tests\BaseTestCase as TestCase;
// Target of the test
use Remix\Fader;
// Utility
use Utility\Reflection\ReflectionStatic;

class FaderTest extends TestCase
{
    public function testLoad(): void
    {
        $fader = new Fader('');
        $this->assertNotNull($fader);
    }

    public function testTranslate(): void
    {
        // Set up the Reflection
        $reflection = new ReflectionStatic(Fader::class);
        $translated = $reflection->executeMethod('translate', ['/:test']);

        //$translated = $this->invokeStaticMethod(Fader::class, 'translate', ['/:test']);
        $this->assertSame('/^\/(?<test>\S+?)\\/?$/', $translated);

        $reflection = new ReflectionStatic(Fader::class);
        $translated = $reflection->executeMethod('translate', ['effector :param1 :param2']);

        //$translated = $this->invokeStaticMethod(Fader::class, 'translate', ['effector :param1 :param2']);
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
