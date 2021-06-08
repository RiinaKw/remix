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
    protected static $shorthandles = [];
    private $effectors = [];

    public function initialize(): self
    {
        static::$shorthandles = Preset\Effector::SHORTHANDLES;
        $app_shorthandles = Audio::getInstance()->preset->get('app.effector.shorthandles', []);
        foreach ($app_shorthandles as $handle => $method) {
            if (isset(static::$shorthandles[$handle])) {
                throw new \Exception("Error: reserved handle : {$handle}");
            }
            static::$shorthandles[$handle] = $method;
        }

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
            $effector_dir = Audio::getInstance()->preset->get('remix.pathes.effector_dir');
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
            if (is_array($target)) {
                list($class, $method) = $target;
            } else {
                $class = $target;
                $method = 'index';
            }
            $instance = $equalizer->instance($class, $this);
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
