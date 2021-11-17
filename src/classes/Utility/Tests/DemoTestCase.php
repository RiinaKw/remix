<?php

namespace Utility\Tests;

use PHPUnit\Framework\TestCase;

abstract class DemoTestCase extends TestCase
{
    protected $daw = null;

    protected function setUp(): void
    {
        $this->daw = \Remix\Audio::getInstance(false)->daw;
    }

    protected function tearDown(): void
    {
        \Remix\Audio::destroy();
    }

    protected function initialize(string $app_dir)
    {
        $this->daw->initialize($app_dir);
        chdir($app_dir . '/..');
    }
}
