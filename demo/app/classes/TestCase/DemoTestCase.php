<?php

namespace RemixDemo\TestCase;

use Utility\Tests\BaseTestCase;
use Remix\Audio;

/**
 * PHPUnit TestCase base class for demo environment.
 *
 * @package  TestCase\Demo
 */
abstract class DemoTestCase extends BaseTestCase
{
    /**
     * DAW intance
     * @var \Remix\Instruments\DAW
     */
    protected $remixDaw = null;

    /**
     * Initialize application.
     */
    protected function setUp(): void
    {
        // Be sure to point to the app directory.
        $app_dir = __DIR__ . '/../..';
        chdir($app_dir . '/..');

        // Initialize for web operation.
        $this->remixDaw = Audio::getInstance()->daw->initialize($app_dir);
    }

    /**
     * Finalize application.
     */
    protected function tearDown(): void
    {
        Audio::destroy();
        Audio::$dead = false;
    }
}
