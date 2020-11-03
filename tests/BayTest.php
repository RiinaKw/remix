<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class BayTest extends TestCase
{
    use \Remix\Utility\Tests\InvokePrivateMethodBehavior;
    use \Remix\Utility\Tests\CaptureOutput;

    protected $bay = null;

    protected function setUp() : void
    {
        require_once(__DIR__ . '/../vendor/autoload.php');

        $remix = \Remix\App::getInstance();
        $this->bay = $this->invokeMethod($remix, 'bay', []);
    }

    public function testLoad()
    {
        // is callable with no arguments?
        $this->startCapture();
        $this->bay->run(['bay']);
        $result = $this->endCapture();

        $this->assertMatchesRegularExpression('/Remix Bay/', $result);

        // is callable with arguments?
        $this->startCapture();
        $this->bay->run(['bay', 'version']);
        $result = $this->endCapture();

        $this->assertMatchesRegularExpression('/Remix framework/', $result);
    }
}
