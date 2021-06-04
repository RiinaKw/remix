<?php

namespace Remix;

use Remix\Utility\Arr;

/**
 * Remix Amp : command line interface
 */
class Amp extends Gear
{
    // Why static instead of const? I wanna read from Presets!
    protected static $namespaces = [
        'remix' => '\\Remix\\Effector\\',
        'app' => '\\App\\Effector\\',
    ];
    protected static $shorthandles = [
        '-v' => \Remix\Effector\Version::class,
        '-h' => \Remix\Effector\Help::class,
    ];

    private $effectors = [];

    private function available($namespace): void
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
                    $this->effectors[$command] = $target;
                }
            } // if (is_file($file))
        }
    }

    public function availableCommands()
    {
        $this->available('remix');
        $this->available('app');

        Effector::line('Available commands :');

        foreach ($this->effectors as $classname) {
            Effector::line('');
            $classname::detail();
        }
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
            $instance = $equalizer->instance($target, $this);
        } else {
            foreach (static::$namespaces as $namespace) {
                $target = $namespace . $class;
                if (class_exists($target)) {
                    $instance = $equalizer->instance($target, $this);
                    break;
                }
            }
        }

        if ($instance) {
            $instance->play($method, $argv);
        } elseif ($class) {
            Effector::line("unknown effector '{$class}'", 'black', 'red');
            Effector::line('try "amp help"');
        } else {
            $instance = $equalizer->instance(Effector\Help::class, $this);
            $instance->index($argv);
        }

        $equalizer = null;
    }
    // function play()
}
// class Bay
