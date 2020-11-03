<?php

namespace Remix;

/**
 * Remix Effector : command line controller
 */
abstract class Effector extends Component
{
    public function __construct()
    {
        \Remix\App::getInstance()->log(__METHOD__);
    } // function __construct()

    public function __destruct()
    {
        \Remix\App::getInstance()->log(__METHOD__);
    }

    public function run(string $method, array $arg = [])
    {
        foreach ($arg as $item) {
            preg_match('/^--(.+?)=(.+)$/', $item, $matches);
            if ($matches) {
                $arg[ $matches[1] ] = $matches[2];
            }
        }

        if ($method) {
            if (method_exists($this, $method)) {
                return $this->$method($arg);
            } else {
                $class = static::class;
                echo "method {$method} not exists in {$class}" . PHP_EOL;
                return $this->index($arg);
            }
        } else {
            return $this->index($arg);
        }
    } // function run()

    public static function line(string $str)
    {
        echo $str . PHP_EOL;
    } // function line()
} // class Effector
