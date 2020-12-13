<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class BayTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateBehavior;
    use \Remix\Utility\Tests\CaptureOutput;

    protected $bay = null;

    protected function setUp(): void
    {
        $remix = \Remix\App::getInstance();
        $this->bay = $this->invokeMethod($remix, 'bay', []);
    }

    public function tearDown(): void
    {
        \Remix\App::destroy();
    }

    public function testLoad()
    {
        // is callable with no arguments?
        $response = $this->capture([$this->bay, 'run'], ['bay']);

        $this->assertMatchesRegularExpression('/Remix Bay/', $response);

        // is callable with arguments?
        $response = $this->capture([$this->bay, 'run'], ['bay', 'version']);

        $this->assertMatchesRegularExpression('/Remix framework/', $response);
    }
}
