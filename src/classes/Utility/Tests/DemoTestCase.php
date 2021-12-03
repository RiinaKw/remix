<?php

namespace Utility\Tests;

use PHPUnit\Framework\TestCase;
use Remix\Audio;

abstract class DemoTestCase extends TestCase
{
    protected $daw = null;

    protected function setUp(): void
    {
        $this->daw = Audio::getInstance()->daw;
    }

    protected function tearDown(): void
    {
        Audio::destroy();
    }
}
