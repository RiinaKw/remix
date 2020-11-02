<?php

namespace Remix;

use PHPUnit\Framework\TestCase;

class RemixDemoTest extends TestCase
{
    protected function setUp() : void
    {
        $root_dir = dirname(dirname(__DIR__));
        $autoload_path = $root_dir . '/vendor/autoload.php';

        require_once($autoload_path);
    }

    public function testInstrument()
    {
        $root_path = __DIR__ . '/../demo';

        $remix = \Remix\App::getInstance();
        $remix->initialize($root_path);

        ob_start();
        $remix->runCli(['bay', 'instrument:acid', '-808', '--add=909']);
        $result = ob_get_clean();

        $this->assertMatchesRegularExpression('/303/', $result);
        $this->assertMatchesRegularExpression('/808/', $result);
        $this->assertMatchesRegularExpression('/909/', $result);
    }
}
