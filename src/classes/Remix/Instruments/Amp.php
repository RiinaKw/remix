<?php

namespace Remix\Instruments;

// Remix core
use Remix\Instrument;
// Effectors
use Remix\Preset\Effector as ShortHandles;
use Remix\Effector;
use Remix\Effectors\Help;
// Exceptions
use Remix\Exceptions\AppException;
// Utilities
use Remix\Utility\Arr;

/**
 * Remix Amp : command line interface.
 *
 * @package  Remix\CLI
 * @todo Write the details.
 * @todo I think Remix\Preset\Effector should not be a class ...
 */
class Amp extends Instrument
{
    private $daw = null;
    private $preset = null;

    private static $namespaces = [];
    protected static $shorthandles = [];
    private $effectors = [];

    public function initialize(DAW $daw): self
    {
        $this->daw = $daw;
        $this->preset = $this->audio->preset;

        static::$shorthandles = ShortHandles::SHORTHANDLES;
        $app_shorthandles = $this->preset->get('app.effector.shorthandles', []);
        foreach ($app_shorthandles as $handle => $method) {
            if (isset(static::$shorthandles[$handle])) {
                throw new AppException("Error: reserved handle : {$handle}");
            }
            static::$shorthandles[$handle] = $method;
        }

        static::$namespaces = [
            'remix' => '\\Remix\\Effectors\\',
            'app' => $this->preset->get('app.namespace') . '\\Effectors\\',
        ];

        $this->load('remix');
        $this->load('app');

        return $this;
    }

    private function load(string $namespace): void
    {
        if ($namespace == 'app') {
            $effector_dir = $this->daw->appDir('classes/Effectors');
        } else {
            $effector_dir = $this->preset->get('remix.pathes.effector_dir');
        }

        foreach (glob($effector_dir . '/{*.php}', GLOB_BRACE) as $file) {
            if (is_file($file)) {
                preg_match('/(?<name>[^\/]+?).php$/', $file, $matches);
                $name = $matches['name'];

                $target = static::$namespaces[$namespace] . $name;
                if (class_exists($target)) {
                    $command = strtolower($name);
                    $this->effectors[$command] = $target;
                }
            } // if (is_file($file))
        }
    }
    // function load()

    public function availableEffector(string $command = '')
    {
        if ($command && ! isset($this->effectors[$command])) {
            Effector::line("unknown effector '{$command}'", 'black', 'red');
        } else {
            Effector::line('Available commands :');

            if ($command) {
                $this->availableCommands($this->effectors[$command]);
            } else {
                foreach ($this->effectors as $classname) {
                    $this->availableCommands($classname);
                }
            }
        }
    }

    public function availableCommands(string $effector)
    {
        $namespaces = explode('\\', $effector);
        $command = strtolower(array_pop($namespaces));

        Effector::line('');
        Effector::line(Effector::color($command, 'green') . ' : ' . $effector::TITLE);

        $outputs = [];
        foreach ($effector::COMMANDS as $key => $item) {
            if ($key) {
                $outputs[] = '    ' .
                    Effector::color($command . ':' . $key, 'yellow') .
                    ' : ' .
                    $item;
            } else {
                $outputs[] = '    ' .
                    Effector::color($command, 'yellow') .
                    ' : ' .
                    $item;
            }
        }
        if ($outputs) {
            Effector::line('  usage :');
            foreach ($outputs as $item) {
                Effector::line($item);
            }
        }
    }

    public function play(array $argv): void
    {
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
            if (is_array($target)) {
                list($class, $method) = $target;
            } else {
                $class = $target;
                $method = 'index';
            }
            $instance = new $class($this);
        } else {
            foreach (static::$namespaces as $namespace) {
                $target = $namespace . $class;
                if (class_exists($target)) {
                    $instance = new $target($this);
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
            $instance = new Help($this);
            $instance->index($argv);
        }
    }
    // function play()
}
// class Bay
