<?php

namespace Remix;

/**
 * Remix Audio : application handler
 */
class Audio
{
    private static $audio = null;
    private $equalizer = null;

    protected static $is_debug = false;
    protected static $is_cli = false;
    protected static $is_console = true;

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
        if (! static::$audio) {
            static::$audio = new static($is_debug);
            static::$audio->initialize();
        }
        return static::$audio;
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

            case 'console':
                static::$is_console = $value;
                break;

            default:
                var_dump('unknown key', $key);
                break;
        }
    }
    // function __set()

    public static function destroy(): void
    {
        if (static::$audio) {
            $bounce_dir = static::$audio->preset->get('remix.bounce_dir');
            $preset = static::$audio->preset->get();

            static::$audio->equalizer->destroy();
            static::$audio->equalizer = null;

            if (static::$is_debug) {
                Delay::logMemory();
                Delay::logTime();

                if (! static::$is_cli && static::$is_console) {
                    $delay =  Delay::get();
                    $view = new Bounce('console', [
                    ], true);
                    $view->delay = $delay;
                    $view->preset = $preset;

                    echo $view->record($bounce_dir . '/console.tpl');
                }
            }
        }
        static::$audio = null;
        Delay::destroy();
    }
    // function destroy()

    public function initialize(): self
    {
        $this->equalizer = Equalizer::factory();
        //set_error_handler([$this, 'errorHandle']);
        set_exception_handler([$this, 'exceptionHandle']);
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
            Effector::line(
                '####' . PHP_EOL .
                '#### ' . $e->getMessage() . PHP_EOL .
                '#### ' . $e->getFile() . ' (' . $e->getLine() . ')' . PHP_EOL .
                '####',
                'white',
                'red'
            );
        } else {
            Studio::recordException($e);
        }
        unset($e);
    }
    // function exceptionHandle()

    public function shutdownHandle(): void
    {
        static::destroy();
        static::$audio = null;
    }
    // function shutdownHandle()
}
// class Audio
