<?php

namespace Remix;

// Remix core
use Remix\Instruments\Equalizer;
use Remix\Tuner;
use Remix\Tuners\Cli as CliTuner;
// Exceptions
use Throwable;
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
    /**
     * The only instance.
     * @var Audio
     */
    private static $audio = null;

    public static $dead = false;

    /**
     * Tuner of debug mode.
     * @var Tuner
     */
    private static $tunerDebug = null;

    /**
     * Equalizer instance.
     * @var Equalizer
     */
    private $equalizer = null;

    /**
     * Tuner of CLI mode.
     * @var CliTuner
     */
    private $tunerCli = null;

    /**** static methods ****/

    /**
     * Get the only instance.
     * @return self
     */
    public static function getInstance(): self
    {
        //$caller = debug_backtrace()[0];
        //var_dump($caller['file'], $caller['line']);

        if (! static::$audio) {
            static::$audio = new static();
        }
        return static::$audio;
    }
    // function getInstance()

    /**
     * Enter debug mode.
     */
    public static function debug(): void
    {
        static::$tunerDebug = new Tuner(true);
    }
    // function debug()

    /**
     * Finish Remix.
     */
    public static function destroy(): void
    {
        if (static::$audio) {
            static::$audio->equalizer = null;

            $id = \Remix\Gear::getId(static::$audio);
            echo "Audio [{$id}] must be down here.<br />\n";
        }

        static::$dead = true;
        static::$audio = null;
    }
    // function destroy()

    /**** non-static methods ****/

    /**
     * Start Remix.
     */
    private function __construct()
    {
        if (static::$dead) {
            echo "<p style='color: red'>Audio : Don't look at me twice</p>\n";
            exit;
        }
        \Remix\Gear::addHash($this);

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

        $this->equalizer = new Equalizer($this);
        $this->registerHandle();
    }
    // function __construct()

    /**
     * Finish Remix.
     */
    public function __destruct()
    {
        \Remix\Gear::removeHash($this);

        Delay::logDeath(static::class);

        $id = spl_object_id($this);
        echo "Audio [{$id}] is down.<br />\n";
    }
    // function __destruct()

    /**
     * Getter.
     * @param  string $key  Key of item
     * @return mixed        Any item
     */
    public function __get(string $key)
    {
        switch ($key) {
            case 'debug':
                return static::$tunerDebug->is();

            case 'cli':
                return $this->tunerCli->cli;

            case 'equalizer':
                return $this->equalizer;

            case 'daw':
            case 'preset':
            case 'mixer':
            case 'amp':
            case 'dj':
                return $this->equalizer->$key;

            default:
                throw new CoreException("Unknown key '{$key}'");
        }
    }
    // function __get()

    /**
     * Set various handlers
     */
    private function registerHandle(): void
    {
        set_error_handler([$this, 'errorHandle']);
        set_exception_handler([$this, 'exceptionHandle']);
        register_shutdown_function([$this, 'shutdownHandle']);
    }
    // function initialize()

    /**
     * Error handring method
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function errorHandle($code, $message, $file, $line, $context = []): void
    {
        throw new ErrorException($message, $code);
    }
    // function errorHandle()

    /**
     * Exception handring method
     * @param [type] $e  [description]
     */

    /**
     * Exception handring method
     * @param Throwable $e  Exception thrown
     */
    public function exceptionHandle(Throwable $e): void
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
        } elseif (! static::$audio) {
            echo "Audio is dead.<br />\n";
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

    /**
     * Shutdown handring method
     */
    public function shutdownHandle(): void
    {
        static::destroy();
        static::$audio = null;
    }
    // function shutdownHandle()
}
// class Audio
