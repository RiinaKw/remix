<?php

namespace Remix;

use PHPUnit\Framework\TestCase;

$app_dir = __DIR__;
$root_dir = dirname($app_dir);
$autoload_path = $root_dir . '/vendor/autoload.php';

require_once($autoload_path);

class RemixBayTest extends TestCase
{
    public function testBay()
    {
        $root_path = __DIR__ . '/../demo';

        $remix = \Remix\App::getInstance();
        $remix->initialize($root_path);

        // is callable with no arguments?
        ob_start();
        $remix->runCli(['bay']);
        $result = ob_get_clean();

        $this->assertMatchesRegularExpression('/Remix Bay/', $result);

        // is callable with arguments?
        ob_start();
        //$remix->runCli(['bay', 'instrument:acid', '-808', '--add=909']);
        $remix->runCli(['bay', 'version']);
        $result = ob_get_clean();

        $this->assertMatchesRegularExpression('/Remix framework/', $result);
    }
}
