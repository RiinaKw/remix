<?php

namespace Remix;

use Remix\Utility\Arr;

/**
 * Remix Amp : command line interface
 */
class Amp extends Gear
{
    private const NAMESPACES = [
        'remix' => '\\Remix\\Effector\\',
        'app' => '\\App\\Effector\\',
    ];

    // Why static instead of const? I wanna read from Presets!
    protected static $shorthandles = [
        '-v' => \Remix\Effector\Version::class,
        '-h' => \Remix\Effector\Help::class,
    ];

    private $effectors = [];

    public function initialize(): self
    {
        $daw = Audio::getInstance()->daw;

        $this->load($daw, 'remix');
        $this->load($daw, 'app');

        return $this;
    }

    private function load(DAW $daw, string $namespace): void
    {
        if ($namespace == 'app') {
            $effector_dir = $daw->appDir('classes/Effector');
        } else {
            $effector_dir = Audio::getInstance()->preset->get('remix.effector_dir');
        }

        foreach (glob($effector_dir . '/{*.php}', GLOB_BRACE) as $file) {
            if (is_file($file)) {
                preg_match('/\/(?<name>.+?).php$/', $file, $matches);
                $name = $matches['name'];

                $target = static::NAMESPACES[$namespace] . $name;
                if (class_exists($target)) {
                    $command = strtolower($name);
                    $this->effectors[$command] = $target;
                }
            } // if (is_file($file))
        }
    }
    // function load()

    public function availableCommands()
    {
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
            foreach (static::NAMESPACES as $namespace) {
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
