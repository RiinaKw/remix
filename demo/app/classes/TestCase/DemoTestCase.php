<?php

namespace Remix\Demo\TestCase;

use Utility\Tests\BaseTestCase;
use Remix\Audio;

/**
 * PHPUnit TestCase base class for demo environment
 *
 * @package  TestCase\Demo
 */
abstract class DemoTestCase extends BaseTestCase
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
