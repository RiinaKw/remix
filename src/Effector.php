<?php

namespace Remix;

/**
 * Remix Effector : command line controller
 */
abstract class Effector extends Component
{
    protected function __construct()
    {
        parent::__construct();
        \Remix\App::getInstance()->logBirth(__METHOD__);
    } // function __construct()

    public function __destruct()
    {
        \Remix\App::getInstance()->logDeath(__METHOD__);
        parent::__destruct();
    }

    public function run(string $method, array $arg = []) : void
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
    } // function run()

    public static function line(string $str) : void
    {
        echo $str . PHP_EOL;
    } // function line()
} // class Effector
