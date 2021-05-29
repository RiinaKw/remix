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

    public function title(): array
    {
        $command = static::classToCommand();
        $outputs = [];
        $outputs[] = "  {$command} : " . static::$title;
        $outputs[] = $this->commands();
        return $outputs;
    }

    public function classToCommand()
    {
        $namespaces = explode('\\', get_class($this));
        return strtolower(array_pop($namespaces));
    }

    public function commands(): array
    {
        $name = $this->classToCommand();
        $outputs = [];
        array_walk(static::$commands, function ($item, $key) use ($name, &$outputs) {
            if ($key) {
                $outputs[] = "    {$name} {$key} : {$item}";
            }
        });
        return $outputs;
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
