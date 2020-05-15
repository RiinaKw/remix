<?php

namespace Remix;

/**
 * Remix App : entry point
 */
class App
{
    protected static $app = null;
    private $container = [];

    protected $root_dir;
    protected $app_dir;

    private function __construct()
    {
    } // function __construct()

    public static function initialize(string $dir)
    {
        $remix = static::getInstance();

        $remix->root_dir = realpath($dir);
        $remix->app_dir = realpath($remix->root_dir . '/app');

        $config = $remix->singleton(\Remix\Config::class);
        $config->load('app');
        $config->load('environment');

        return $remix;
    } // function initialize()

    public static function getInstance()
    {
        if (! static::$app) {
            static::$app = new self;
        }
        return static::$app;
    } // function getInstance()

    protected function singleton(string $class)
    {
        $remix = static::getInstance();
        if (! array_key_exists($class, $this->container)) {
            $this->container[$class] = $remix->factory($class);
        }
        return $this->container[$class];
    } // function singleton()

    public function factory(string $class)
    {
        return new $class;
    } // function factory()

    public function dir(string $path)
    {
        return realpath($this->root_dir . '/' . $path);
    }

    public function appDir(string $path = '')
    {
        return realpath($this->app_dir . '/' . $path);
    } // function appDir()

    public function config()
    {
        return $this->singleton(\Remix\Config::class);
    } // function config()

    protected function mixer()
    {
        return $this->singleton(\Remix\Mixer::class);
    } // function mixer()

    protected function bay()
    {
        return $this->singleton(\Remix\Bay::class);
    } // function bay()

    public function runWeb()
    {
        try {
            $path = $_SERVER['PATH_INFO'] ?? '';
            $this->mixer()->route($path);
        } catch (\Remix\Exceptions\HttpException $e) {
            $e->render();
        }
    } // function runWeb()

    public function runCli(array $argv)
    {
        $this->bay()->run($argv);
    } // function runCli()
} // class App
