<?php

namespace Remix;

/**
 * Remix Audio : entry point
 */
class Audio
{
    private static $app = null;
    private $equalizer = null;
    //private $delay = null;

    protected static $is_debug = false;
    protected static $is_cli = false;

    private function __construct(bool $is_debug)
    {
        static::$is_debug = $is_debug;
        static::$is_cli = (php_sapi_name() == 'cli');
        Delay::getInstance(static::$is_debug, static::$is_cli);
        if ($is_debug) {
            Delay::logMemory();
        }
    }
    // function __construct()

    public function __destruct()
    {
    }
    // function __destruct()

    public static function getInstance(bool $is_debug = false): self
    {
        if (! static::$app) {
            static::$app = new static($is_debug);
            static::$app->initialize();
        }
        return static::$app;
    }
    // function getInstance()

    public function __get($key)
    {
        switch ($key) {
            case 'debug':
                return static::$is_debug;

            case 'cli':
                return static::$is_cli;

            case 'equalizer':
                return $this->equalizer;

            case 'daw':
                return $this->equalizer->singleton(DAW::class);

            case 'preset':
                return $this->equalizer->singleton(Preset::class);

            case 'mixer':
                return $this->equalizer->singleton(Mixer::class);

            case 'amp':
                return $this->equalizer->singleton(Amp::class);

            case 'dj':
                return $this->equalizer->singleton(DJ::class);

            default:
                var_dump('unknown key', $key);
                return null;
        }
    }
    // function __get()

    public function __set(string $key, $value)
    {
        switch ($key) {
            case 'cli':
                static::$is_cli = $value;
                break;

            default:
                var_dump('unknown key', $key);
                break;
        }
    }
    // function __set()

    public static function destroy(): void
    {
        if (static::$app) {
            static::$app->equalizer->destroy();
            static::$app->equalizer = null;

            if (static::$is_debug) {
                Delay::logMemory();
                Delay::logTime();
                if (! static::$app->cli) {
                    echo Delay::get();
                }
            }
        }
        static::$app = null;
        Delay::destroy();
    }
    // function destroy()

    public function initialize(): self
    {
        $this->equalizer = Equalizer::factory();
        //set_error_handler([$this, 'errorHandle']);
        //set_exception_handler([$this, 'exceptionHandle']);
        register_shutdown_function([$this, 'shutdownHandle']);

        return $this;
    }
    // function initialize()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function errorHandle($code, $message, $file, $line, $context = []): void
    {
        throw new Exceptions\ErrorException($message, $code);
    }
    // function errorHandle()

    public function exceptionHandle($e): void
    {
        if (static::$is_cli) {
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
    }
    // function exceptionHandle()

    public function shutdownHandle(): void
    {
        static::destroy();
        static::$app = null;
    }
    // function shutdownHandle()
}
// class Audio
