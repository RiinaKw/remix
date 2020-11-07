<?php

namespace Remix;

use \Remix\Utility\Performance\Memory;

/**
 * Remix App : entry point
 */
class App
{
    protected static $app = null;
    protected $cli = true;
    protected $debug = false;
    private $container = [];

    protected $root_dir;
    protected $app_dir;
    protected $public_dir;

    private function __construct(bool $is_debug)
    {
        $this->debug = $is_debug;
        $this->logWithMemory(__METHOD__);
    } // function __construct()

    public function __destruct()
    {
        $debug = $this->isDebug();
        static::destroy();

        if ($this->isDebug()) {
            echo '  ', __METHOD__, PHP_EOL;
            Memory::get(__METHOD__);
        }
    }

    public function isDebug() : bool
    {
        return $this->debug;
    }

    public function log(string $str)
    {
        if ($this->isDebug()) {
            echo '  ', $str, PHP_EOL;
        }
    }

    public function logWithMemory(string $str)
    {
        if ($this->isDebug()) {
            echo '  ', $str, PHP_EOL;
            Memory::get();
        }
    }

    public static function initialize(string $dir) : App
    {
        $remix = static::getInstance();

        //set_error_handler([$remix, 'errorHandle']);
        //set_exception_handler([$remix, 'exceptionHandle']);
        register_shutdown_function([$remix, 'shutdownHandle']);

        $remix->root_dir = realpath($dir);
        $remix->app_dir = realpath($remix->root_dir . '/app');

        $env = require($remix->app_dir . '/env.php');
        $env = ($env && $env !== 1) ? $env : 'production';

        $config = $remix->singleton(Config::class);
        $config->set('env.name', $env);
        $config->load('app');
        $config->load('env.' . $env, 'env.config');

        $remix->dj();

        return $remix;
    } // function initialize()

    public static function getInstance(bool $is_debug = false) : App
    {
        if (! static::$app) {
            static::$app = new self($is_debug);
        }
        return static::$app;
    } // function getInstance()

    protected function singleton(string $class) : Component
    {
        $remix = static::getInstance();
        if (! array_key_exists($class, $this->container)) {
            $this->container[$class] = $remix->factory($class);
        }
        return $this->container[$class];
    } // function singleton()

    public function factory(string $class) : Component
    {
        return new $class;
    } // function factory()

    public function dir(string $path) : string
    {
        return realpath($this->root_dir . '/' . $path);
    } // function dir()

    public function appDir(string $path = '') : string
    {
        return realpath($this->app_dir . '/' . $path);
    } // function appDir()

    public function publicDir(string $path = '') : string
    {
        return realpath($this->public_dir . '/' . $path);
    } // function publicDir()

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

    public function dj() : DJ
    {
        return $this->singleton(DJ::class);
    }

    public function runWeb(string $public_dir) : Studio
    {
        $this->public_dir = $public_dir;
        $this->cli = false;
        $path = $_SERVER['PATH_INFO'] ?? '';

        $tracks_path = $this->appDir('/mixer.php') ?: [];
        $studio = $this->mixer()->load($tracks_path)->route($path);
        return $studio;
    } // function runWeb()

    public function runCli(array $argv) : void
    {
        $this->cli = true;
        $this->bay()->run($argv);
    } // function runCli()

    public function isWeb() : bool
    {
        return ! $this->cli;
    } // function isCli()

    public function isCli() : bool
    {
        return $this->cli;
    } // function isCli()

    public static function destroy() : void
    {
        $remix = static::$app;
        \Remix\DJ::destroy();

        if (isset($remix->container)) {
            foreach ($remix->container as $key => $item) {
                $remix->container[$key] = null;
            }
            $remix->container = [];
        }
        static::$app = null;
    } // function destroy()

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
            Studio::recordException($e);
        }
        var_dump(debug_backtrace());
    } // function exceptionHandle()

    public function shutdownHandle()
    {
        self::destroy();
    } // function shutdownHandle()
} // class App
