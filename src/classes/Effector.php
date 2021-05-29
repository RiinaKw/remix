<?php

namespace Remix;

/**
 * Remix Effector : command line controller
 */
abstract class Effector extends Gear
{
    protected static $title = 'this eccector is abstract class';
    protected static $commands = [
        '' => 'nothing to do',
    ];

    public function detail()
    {
        Effector::line(static::$title);
        Effector::line('usage:');
        $this->commands();
    }

    public function commands(): void
    {
        $namespaces = explode('\\', get_class($this));
        $name = strtolower(array_pop($namespaces));
        array_walk(static::$commands, function ($item, $key) use ($name) {
            Effector::line("  {$name} {$key} : {$item}");
        });
    }

    public function play(string $method, array $arg = []): void
    {
        foreach ($arg as $item) {
            preg_match('/^--(.+?)=(.+)$/', $item, $matches);
            if ($matches) {
                $arg[ $matches[1] ] = $matches[2];
            }
        }

        if ($method) {
            if (method_exists($this, $method)) {
                $this->$method($arg);
            } else {
                $class = static::class;
                echo "method {$method} not exists in {$class}" . PHP_EOL;
                $this->index($arg);
            }
        } else {
            $this->index($arg);
        }
    }
    // function play()

    public static function line(string $str): void
    {
        echo $str . PHP_EOL;
    }
    // function line()
}
// class Effector
