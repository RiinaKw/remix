<?php

namespace Remix;

use Remix\Utility\Arr;

/**
 * Remix Amp : command line interface
 */
class Amp extends Gear
{
    protected static $namespaces = [
        'remix' => '\\Remix\\Effector\\',
        'app' => '\\App\\Effector\\',
    ];

    protected static $shorthandles = [
        '-v' => \Remix\Effector\Version::class,
        '-h' => \Remix\Effector\Help::class,
    ];

    private static $effectors = [];

    private static function available($namespace): void
    {
        $daw = Audio::getInstance()->daw;
        if ($namespace == 'app') {
            $effector_dir = $daw->appDir('classes/Effector');
        } else {
            $effector_dir = Audio::getInstance()->preset->get('remix.effector_dir');
        }

        foreach (glob($effector_dir . '/{*.php}', GLOB_BRACE) as $file) {
            if (is_file($file)) {
                preg_match('/\/(?<name>.+?).php$/', $file, $matches);
                $name = $matches['name'];

                $target = static::$namespaces[$namespace] . $name;
                if (class_exists($target)) {
                    $command = strtolower($name);
                    static::$effectors[$command] = $target;
                }
            } // if (is_file($file))
        }
    }

    public static function availableCommands()
    {
        static::available('remix');
        static::available('app');

        Effector::line('Available commands :');
        Effector::line('');

        $outputs = [];
        array_walk(static::$effectors, function ($classname) use (&$outputs) {
            $effector = new $classname();
            $outputs[] = $effector->title();
            $outputs[] = '';
        });
        array_pop($outputs);
        $lines = Arr::flatten($outputs);
        array_walk($lines, function ($line) {
            Effector::line($line);
        });
    }

    public function play(array $argv): void
    {
        $equalizer = Audio::getInstance()->equalizer;
        array_shift($argv);

        $instance = null;
        $arg0 = $argv[0] ?? '';
        array_shift($argv);
        if (strpos($arg0, ':') !== false) {
            list($class, $method) = explode(':', $arg0);
        } else {
            $class = $arg0;
            $method = '';
        }

        if (array_key_exists($class, static::$shorthandles)) {
            $target = static::$shorthandles[$class];
            $instance = $equalizer->instance($target);
        } else {
            foreach (static::$namespaces as $namespace) {
                $target = $namespace . $class;
                if (class_exists($target)) {
                    $instance = $equalizer->instance($target);
                    break;
                }
            }
        }

        if ($instance) {
            $instance->play($method, $argv);
        } elseif ($class) {
            echo 'unknown effector : ' . $class . PHP_EOL;
            echo 'try "amp help"' . PHP_EOL;
        } else {
            $instance = $equalizer->instance(Effector\Help::class);
            $instance->index($argv);
        }

        $equalizer = null;
    }
    // function play()
}
// class Bay
