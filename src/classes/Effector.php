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

    protected static $text_colors = array(
        'black'        => '0;30',
        'dark_gray'    => '1;30',
        'blue'         => '0;34',
        'dark_blue'    => '1;34',
        'light_blue'   => '1;34',
        'green'        => '0;32',
        'light_green'  => '1;32',
        'cyan'         => '0;36',
        'light_cyan'   => '1;36',
        'red'          => '0;31',
        'light_red'    => '1;31',
        'purple'       => '0;35',
        'light_purple' => '1;35',
        'light_yellow' => '0;33',
        'yellow'       => '1;33',
        'light_gray'   => '0;37',
        'white'        => '1;37',
    );
    protected static $background_colors = array(
        'black'      => '40',
        'red'        => '41',
        'green'      => '42',
        'yellow'     => '43',
        'blue'       => '44',
        'magenta'    => '45',
        'cyan'       => '46',
        'light_gray' => '47',
    );

    public function title(): array
    {
        $command = static::classToCommand();
        $outputs = [];
        $outputs[] = '  ' . Effector::color($command, 'green') . ' : ' . static::$title;
        $outputs[] = $this->commands();
        return $outputs;
    }
    // function title()

    private function classToCommand()
    {
        $namespaces = explode('\\', get_class($this));
        return strtolower(array_pop($namespaces));
    }
    // function classToCommand()

    private function commands(): array
    {
        $name = $this->classToCommand();
        $outputs = [];
        array_walk(static::$commands, function ($item, $key) use ($name, &$outputs) {
            if ($key) {
                $outputs[] = '    '
                    . Effector::color($name . ':' . $key, 'yellow')
                    . ' : '
                    . $item;
            }
        });
        return $outputs;
    }
    // function commands()

    public function play(string $method, array $args = []): void
    {
        foreach ($args as $item) {
            preg_match('/^--(.+?)=(.+)$/', $item, $matches);
            if ($matches) {
                $args[ $matches[1] ] = $matches[2];
            }
        }

        if ($method) {
            if (method_exists($this, $method)) {
                $this->$method($args);
                return;
            } else {
                $class = static::class;
                Effector::line("method '{$method}' not exists in '{$class}'", 'black', 'red');
            }
        }
        $this->index($args);
    }
    // function play()

    public static function line(
        string $text,
        string $text_color = '',
        string $background_color = ''
    ): void {
        if ($text_color || $background_color) {
            $text = static::color($text, $text_color, $background_color);
        }
        echo $text . PHP_EOL;
    }
    // function line()

    public static function lineError(
        string $text,
        string $text_color = '',
        string $background_color = ''
    ): void {
        if ($text_color || $background_color) {
            $text = static::color($text, $text_color, $background_color);
        }
        fprintf(STDERR, $text . PHP_EOL);
    }
    // function line()

    public static function color(
        string $text,
        string $text_color = '',
        string $background_color = ''
    ): string {
        $left = '';
        $right = '';

        if ($text_color && ! isset(static::$text_colors[$text_color])) {
            throw new RemixException("unknown color : '{$text_color}'");
        }
        if ($background_color && ! isset(static::$background_colors[$background_color])) {
            throw new RemixException("unknown color : '{$background_color}'");
        }

        $left = "\033[" . static::$text_colors[$text_color] . "m";
        if ($background_color) {
            $left .= "\033[" . static::$background_colors[$background_color] . "m";
        }
        $right = "\033[0m";
        return $left . $text . $right;
    }
    // function color()
}
// class Effector
