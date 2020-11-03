<?php

namespace Remix\CoreTests;

use PHPUnit\Framework\TestCase;

class RemixBayTest extends TestCase
{
    protected $bay = null;

    protected function setUp() : void
    {
        require_once(__DIR__ . '/../vendor/autoload.php');

        $remix = \Remix\App::getInstance();

        $reflection = new \ReflectionClass($remix);
        $method = $reflection->getMethod('bay');
        $method->setAccessible(true);
        $this->bay = $method->invokeArgs($remix, ['bay']);
    }

    public function testRemixLoad()
    {
        // is callable with no arguments?
        ob_start();
        $this->bay->run(['bay']);
        $result = ob_get_clean();

        $this->assertMatchesRegularExpression('/Remix Bay/', $result);

        // is callable with arguments?
        ob_start();
        $this->bay->run(['bay', 'version']);
        $result = ob_get_clean();

        $this->assertMatchesRegularExpression('/Remix framework/', $result);
    }
}
