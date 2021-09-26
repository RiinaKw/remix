<?php

namespace Remix;

/**
 * Remix Audio : application handler
 *
 * @package  Remix\Core
 * @todo Write the details.
 */
class Audio
{
    private static $audio = null;
    private $equalizer = null;

    protected static $is_debug = false;
    protected static $is_cli = false;

    private function __construct(bool $is_debug)
    {
        static::$is_debug = $is_debug;
        static::$is_cli = (php_sapi_name() == 'cli');
        Delay::start(static::$is_debug, static::$is_cli);
        if ($is_debug) {
            Delay::logMemory();
        }
        Delay::logBirth(static::class);

        $this->equalizer = new Instruments\Equalizer();
        $this->registerHandle();
    }
    // function __construct()

    public function __destruct()
    {
        Delay::logDeath(static::class);
    }
    // function __destruct()

    public static function getInstance(bool $is_debug = false): self
    {
        if (! static::$audio) {
            static::$audio = new static($is_debug);
        }
        return static::$audio;
    }
    // function getInstance()

    private function singleton(string $class): ?Gear
    {
        return $this->equalizer ? $this->equalizer->singleton($class) : null;
    }

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
                return $this->singleton(Instruments\DAW::class);

            case 'preset':
                return $this->singleton(Instruments\Preset::class);

            case 'mixer':
                return $this->singleton(Instruments\Mixer::class);

            case 'amp':
                return $this->singleton(Instruments\Amp::class);

            case 'dj':
                return $this->singleton(Instruments\DJ::class);

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
        if (static::$audio) {
            static::$audio->equalizer = null;
        }
        static::$audio = null;
    }
    // function destroy()

    private function registerHandle(): void
    {
        //set_error_handler([$this, 'errorHandle']);
        set_exception_handler([$this, 'exceptionHandle']);
        register_shutdown_function([$this, 'shutdownHandle']);
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
            static::destroy();
        } else {
            $preset = static::$audio->preset;
            static::destroy();
            static::$audio = null;

            $reverb = Reverb::exeption($e, $preset);
            unset($preset);
            echo $reverb;
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
