<?php

namespace Remix;

use \Remix\Utility\Performance\Memory;

/**
 * Remix App : entry point
 */
class App
{
    protected static $app = null;
    protected $env;
    protected $cli = true;
    protected $debug = false;
    private $container = [];

    protected $root_dir;
    protected $app_dir;

    private function __construct($is_debug)
    {
        $this->debug = $is_debug;
        $this->logWithMemory(__METHOD__);
    } // function __construct()

    public function __destruct()
    {
        $this->container = null;
        static::$app = null;

        $this->logWithMemory(__METHOD__);
    }

    public function isDebug()
    {
        return $this->debug;
    }

    public function log($str)
    {
        if ($this->isDebug()) {
            echo '  ', $str, PHP_EOL;
        }
    }

    public function logWithMemory($str)
    {
        if ($this->isDebug()) {
            echo '  ', $str, PHP_EOL;
            Memory::get();
        }
    }

    public static function initialize(string $dir) : App
    {
        $remix = static::getInstance();

        set_error_handler([$remix, 'errorHandle']);
        set_exception_handler([$remix, 'exceptionHandle']);
        register_shutdown_function([$remix, 'shutdownHandle']);

        $remix->root_dir = realpath($dir);
        $remix->app_dir = realpath($remix->root_dir . '/app');

        $env = require($remix->app_dir . '/env.php');
        $remix->env = ($env && $env !== 1) ? $env : 'production';

        $config = $remix->singleton(Config::class);
        $config->load('app');
        $config->load('env.' . $remix->env, 'env.config');
        exit;

        return $remix;
    } // function initialize()

    public static function getInstance($is_debug = false) : App
    {
        if (! static::$app) {
            static::$app = new self($is_debug);
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

    public function dir(string $path) : string
    {
        return realpath($this->root_dir . '/' . $path);
    }

    public function appDir(string $path = '') : string
    {
        return realpath($this->app_dir . '/' . $path);
    } // function appDir()

    public function config() : Config
    {
        return $this->singleton(Config::class);
    } // function config()

    public function mixer() : Mixer
    {
        return $this->singleton(Mixer::class);
    } // function mixer()

    protected function bay() : Bay
    {
        return $this->singleton(Bay::class);
    } // function bay()

    public function runWeb()
    {
        $this->cli = false;
        $path = $_SERVER['PATH_INFO'] ?? '';

        $tracks_path = $this->appDir('/mixer.php') ?: [];
        $studio = $this->mixer()->load($tracks_path)->route($path);
        echo $studio;
    } // function runWeb()

    public function runCli(array $argv)
    {
        $this->cli = true;
        $this->bay()->run($argv);
    } // function runCli()

    public function isWeb()
    {
        return ! $this->cli;
    } // function isCli()

    public function isCli()
    {
        return $this->cli;
    } // function isCli()

    public function errorHandle($code, $message, $file, $line, $context = [])
    {
        throw new Exceptions\ErrorException($message, $code);
    } // function errorHandle()

    public function exceptionHandle($e)
    {
        if ($this->isCli()) {
            Effector::line('####');
            Effector::line('#### ' . $e->getMessage());
            Effector::line('#### ' . $e->getFile() . ' line ' . $e->getLine());
            Effector::line('####');
        } else {
            Studio::renderException($e);
        }
    } // function exceptionHandle()

    public function shutdownHandle()
    {
    } // function shutdownHandle()
} // class App
