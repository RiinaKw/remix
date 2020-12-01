<?php

namespace Remix;

/**
 * Remix Amp : command line interface
 */
class Amp extends Gear
{
    protected static $shorthandles = [
        '-v' => \Remix\Effector\Version::class,
        '-h' => \Remix\Effector\Help::class,
    ];

    public function run(array $argv): void
    {
        $equalizer = App::getInstance()->equalizer;
        array_shift($argv);

        $namespaces = [
            '\\Remix\\Effector\\',
            '\\App\\Effector\\',
        ];

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
            foreach ($namespaces as $namespace) {
                $target = $namespace . $class;
                if (class_exists($target)) {
                    $instance = $equalizer->instance($target);
                    break;
                }
            }
        }

        if ($instance) {
            $instance->run($method, $argv);
        } elseif ($class) {
            echo 'unknown effector : ' . $class . PHP_EOL;
            echo 'try "bay help"' . PHP_EOL;
        } else {
            $instance = $equalizer->instance(Effector\Help::class);
            $instance->index($argv);
        }

        $equalizer = null;
    }
    // function run()
}
// class Bay
