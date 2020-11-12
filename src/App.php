<?php

namespace Remix;

use \Remix\Utility\Performance\Memory;
use \Remix\Utility\Performance\Time;

/**
 * Remix App : entry point
 */
class App
{
    protected static $app = null;
    protected $cli = true;
    protected $debug = false;
    private $container = [];

    private $log = [];
    private $time = null;

    protected $root_dir;
    protected $app_dir;
    protected $public_dir;

    private function __construct(bool $is_debug)
    {
        if ($is_debug) {
            $this->time = new Time;
            $this->time->start();
            $this->log($is_debug, Memory::get());
            $this->log(true, __METHOD__, '+');
        }
        $this->debug = $is_debug;
    } // function __construct()

    public function __destruct()
    {
        $cli = $this->isCli();
        $debug = $this->isDebug();

        if ($debug) {
            $this->logDeath(__METHOD__, '-');
            $this->time->stop();
            $this->log(true, Memory::get(__METHOD__));
            $this->log(true, (string)$this->time);

            if ($cli) {
                echo implode(PHP_EOL, $this->log);
            } else {
                echo '<pre>', implode(PHP_EOL, $this->log), '</pre>';
            }
        }
    }

    public function isDebug() : bool
    {
        return $this->debug;
    }

    protected function log(bool $show, string $str, string $flag = '')
    {
        if ($show) {
            $flag = $flag ? sprintf('[%s]', $flag) : '';
            $this->log[] =  $flag . ' ' . $str;
        }
    } // function log()

    public function logBirth(string $str)
    {
        $debug = $this->isDebug();
        $this->log($debug, $str, '+');
    } // function logBirth()

    public function logDeath(string $str)
    {
        $debug = $this->isDebug();
        $this->log($debug, $str, '-');
    } // function logDeath()

    public function logMemory(string $str)
    {
        $this->log($this->isDebug(), Memory::get());
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

    public function factory(string $class, $args = null) : Component
    {
        return $class::factory($args);
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
        $mixer = $this->mixer();
        $studio = $mixer->load($tracks_path)->route($path);
        static::log(true, '[body]');
        $mixer->destroy();
        return $studio;
    } // function runWeb()

    public function runCli(array $argv) : void
    {
        $this->cli = true;
        $this->bay()->run($argv);
        static::log(true, '[body]');
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

        if ($remix && $remix->container) {
            foreach ($remix->container as $key => $item) {
                if (method_exists($item, 'destroy')) {
                    $item->destroy();
                }
                $item = null;
                unset($remix->container[$key]);
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
        unset($e);
    } // function exceptionHandle()

    public function shutdownHandle()
    {
        static::destroy();
    } // function shutdownHandle()
} // class App
