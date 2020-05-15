<?php

namespace Remix;

/**
 * Remix Bay : command line interface
 */
class Bay extends \Remix\Component
{

    protected static $shorthandles = [
        '-v' => \Remix\Effector\Version::class,
        '-h' => \Remix\Effector\Help::class,
    ];

    public function run(array $argv)
    {
        $remix = \Remix\App::getInstance();
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
            $instance = $remix->factory($target);
        } else {
            foreach ($namespaces as $namespace) {
                $target = $namespace . $class;
                if (class_exists($target)) {
                    $instance = $remix->factory($target);
                    break;
                }
            }
        }

        if ($instance) {
            $instance->run($method, $argv);
        } else {
            $instance = $remix->factory(\Remix\Effector\Help::class);
            $instance->index($argv);
        }
    } // function run()
} // class Bay
