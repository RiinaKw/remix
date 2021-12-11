<?php

namespace Remix;

use Remix\Instruments\{
    Equalizer,
    DAW,
    Preset,
    Mixer,
    Amp,
    DJ
};
use Remix\Tuner;
use Remix\Tuners\Cli as CliTuner;
use Remix\Exceptions\{
    CoreException,
    ErrorException
};

/**
 * Remix Audio : application handler.
 *
 * @package  Remix\Core
 * @todo Write the details.
 */
class Audio
{
    private static $audio = null;
    private $equalizer = null;

    private $tunerCli = null;
    private static $tunerDebug = null;

    private function __construct()
    {
        $this->tunerCli = new CliTuner(php_sapi_name());
        if (! static::$tunerDebug) {
            static::$tunerDebug = new Tuner(false);
        }

        if (static::$tunerDebug->is()) {
            Delay::isDebug();
        }
        if ($this->tunerCli->cli) {
            Delay::isCli();
        }
        Delay::start();
        if (static::$tunerDebug->is()) {
            Delay::logMemory();
        }
        Delay::logBirth(static::class);

        $this->equalizer = new Equalizer();
        $this->registerHandle();
    }
    // function __construct()

    public function __destruct()
    {
        Delay::logDeath(static::class);
    }
    // function __destruct()

    public static function getInstance(): self
    {
        if (! static::$audio) {
            static::$audio = new static();
        }
        return static::$audio;
    }
    // function getInstance()

    public static function isDebug(): void
    {
        static::$tunerDebug = new Tuner(true);
    }

    private function singleton(string $class): ?Gear
    {
        return $this->equalizer ? $this->equalizer->singleton($class) : null;
    }

    public function __get($key)
    {
        switch ($key) {
            case 'debug':
                return static::$tunerDebug->is();

            case 'cli':
                return $this->tunerCli->cli;

            case 'equalizer':
                return $this->equalizer;

            case 'daw':
                return $this->singleton(DAW::class);

            case 'preset':
                return $this->singleton(Preset::class);

            case 'mixer':
                return $this->singleton(Mixer::class);

            case 'amp':
                return $this->singleton(Amp::class);

            case 'dj':
                return $this->singleton(DJ::class);

            default:
                throw new CoreException("Unknown key '{$key}'");
        }
    }
    // function __get()

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
        set_error_handler([$this, 'errorHandle']);
        set_exception_handler([$this, 'exceptionHandle']);
        register_shutdown_function([$this, 'shutdownHandle']);
    }
    // function initialize()

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function errorHandle($code, $message, $file, $line, $context = []): void
    {
        throw new ErrorException($message, $code);
    }
    // function errorHandle()

    public function exceptionHandle($e): void
    {
        if ($this->tunerCli->cli) {
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
