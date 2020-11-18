<?php

namespace Remix;

/**
 * Remix App : entry point
 */
class App extends Component
{
    private static $app = null;
    private static $equalizer = null;
    private static $delay = null;
    private static $time = null;

    protected static $is_cli = null;

    protected static $debug = false;

    protected $root_dir;
    protected $app_dir;
    protected $public_dir;

    private function __construct(bool $is_debug)
    {
        if (! static::$delay) {
            static::$delay = new Delay($is_debug);
        }
        if ($is_debug) {
            static::$delay->logMemory();
        }
        static::$debug = $is_debug;

        static::$app = $this;
        static::$is_cli = (php_sapi_name() == 'cli');
        parent::__construct();

        static::$equalizer = Equalizer::factory();
    } // function __construct()

    public function __destruct()
    {
        parent::__destruct();

        if (static::isDebug()) {
            static::$delay->logMemory();
            static::$delay->logTime();
        }
        static::$delay = null;
    }

    public static function getInstance(bool $is_debug = false) : App
    {
        if (! static::$app) {
            new self($is_debug);
        }
        return static::$app;
    } // function getInstance()

    public static function destroy() : void
    {
        if (static::$equalizer) {
            static::$equalizer->destroy();
            static::$equalizer = null;
        }

        static::$app = null;
    } // function destroy()

    public static function isDebug() : bool
    {
        return static::$debug;
    }

    public function isCli() : bool
    {
        return static::$is_cli;
    }

    protected static function log(bool $show, string $type, string $str, string $flag = '')
    {
        if (static::isDebug()) {
            static::$delay->log($type, $str, $flag);
        }
    } // function log()

    public static function logBirth(string $str)
    {
        static::log(true, 'TRACE', $str, '+');
    } // function logBirth()

    public static function logDeath(string $str)
    {
        static::log(true, 'TRACE', $str, '-');
    } // function logDeath()

    public static function logMemory(string $str)
    {
        static::log(true, 'MEMORY', Memory::get());
    } // function logMemory()

    public static function initialize(string $dir) : App
    {
        $remix = static::getInstance();

        //set_error_handler([$remix, 'errorHandle']);
        set_exception_handler([$remix, 'exceptionHandle']);
        register_shutdown_function([$remix, 'shutdownHandle']);

        $remix->root_dir = realpath($dir);
        $remix->app_dir = realpath($remix->root_dir . '/app');

        $env = require($remix->app_dir . '/env.php');
        $env = ($env && $env !== 1) ? $env : 'production';

        $config = $remix->equalizer()->singleton(Config::class);
        $config->set('env.name', $env);
        $config->load('app');
        $config->load('env.' . $env, 'env.config');

        $remix->dj();

        return $remix;
    } // function initialize()

    public function equalizer() : Equalizer
    {
        if (! static::$equalizer) {
            static::$equalizer = Equalizer::factory();
        }
        return static::$equalizer;
    } // function equalizer()

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
        return $this->equalizer()->singleton(Config::class);
    } // function config()

    public function mixer() : Mixer
    {
        return $this->equalizer()->singleton(Mixer::class);
    } // function mixer()

    protected function bay() : Bay
    {
        return $this->equalizer()->singleton(Bay::class);
    } // function bay()

    public function dj() : DJ
    {
        return $this->equalizer()->singleton(DJ::class);
    }

    public function runWeb(string $public_dir) : Studio
    {
        static::$is_cli = false;

        $this->public_dir = $public_dir;
        $path = $_SERVER['PATH_INFO'] ?? '';

        $tracks_path = $this->appDir('/mixer.php') ?: [];
        $mixer = $this->mixer();
        $studio = $mixer->load($tracks_path)->route($path);
        static::log(true, 'BODY', '');
        $mixer->destroy();
        return $studio;
    } // function runWeb()

    public function runCli(array $argv) : void
    {
        $this->bay()->run($argv);
        static::log(true, 'BODY', '');
    } // function runCli()

    public function errorHandle($code, $message, $file, $line, $context = [])
    {
        throw new Exceptions\ErrorException($message, $code);
    } // function errorHandle()

    public function exceptionHandle($e)
    {
        if ($this->isCli()) {
            echo "\033[41m";
            Effector::line('####');
            Effector::line('#### ' . $e->getMessage());
            Effector::line('#### ' . sprintf('%s (%d)', $e->getFile(), $e->getLine()));
            Effector::line('####');
            echo "\033[0m";
        } else {
            Studio::recordException($e);
        }
        var_dump(debug_backtrace());
        unset($e);
    } // function exceptionHandle()

    public function shutdownHandle()
    {
        static::destroy();
    } // function shutdownHandle()
} // class App
