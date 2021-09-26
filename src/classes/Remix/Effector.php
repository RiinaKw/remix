<?php

namespace Remix;

use Remix\Instruments\Amp;

/**
 * Remix Effector : command line controller
 *
 * @package  Remix\CLI
 */
abstract class Effector extends Gear
{
    /**
     * Parent Amp object.
     * @var \Remix\Instruments\Amp
     */
    protected $amp = null;

    /**
     * Its own title.
     * @var string
     */
    public const TITLE = 'this eccector is abstract class';

    /**
     * Its own available commands and descriptions.
     * @var string[]
     */
    public const COMMANDS = [
        '' => 'nothing to do',
    ];

    /**
     * Text colors for CLI
     * @var string[]
     * @todo Do I need to define it in this class? Shouldn't it rather be defined in Amp?
     */
    private const TEXT_COLORS = array(
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

    /**
     * Background colors for CLI
     * @var string[]
     * @todo Do I need to define it in this class? Shouldn't it rather be defined in Amp?
     */
    private const BACKGROUND_COLORS = array(
        'black'      => '40',
        'red'        => '41',
        'green'      => '42',
        'yellow'     => '43',
        'blue'       => '44',
        'magenta'    => '45',
        'cyan'       => '46',
        'light_gray' => '47',
    );

    /**
     * Specify the parent Amp.
     * @param Amp $amp  Amp object
     */
    public function __construct(Amp $amp)
    {
        parent::__construct();
        $this->amp = $amp;
    }

    /**
     * Default command : nothing to do.
     *
     * @todo: Return value
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function index($arg)
    {
        Effector::line('nothing to do', 'black', 'yellow');
    }

    /**
     * Execute the command.
     *
     * @param string $method  Method of command
     * @param array  $args    Arguments of command
     */
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

    /**
     * Output a line to the CLI
     *
     * @param string $text              Content of line
     * @param string $text_color        Text color
     * @param string $background_color  Background color
     * @todo Do I need to define it in this class? Because it might conflict with routing.
     */
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

    /**
     * Output a line to the STDERR
     *
     * @param string $text              Content of line
     * @param string $text_color        Text color
     * @param string $background_color  Background color
     * @todo Do I need to define it in this class? Because it might conflict with routing.
     */
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

    /**
     * Color the text
     *
     * @param string $text              Source text
     * @param string $text_color        Text color
     * @param string $background_color  Background color
     * @return string                   Colored text
     * @todo Do I need to define it in this class? Because it might conflict with routing.
     */
    public static function color(
        string $text,
        string $text_color = '',
        string $background_color = ''
    ): string {
        $left = '';
        $right = '';

        if ($text_color && ! isset(self::TEXT_COLORS[$text_color])) {
            throw new RemixException("unknown color : '{$text_color}'");
        }
        if ($background_color && ! isset(self::BACKGROUND_COLORS[$background_color])) {
            throw new RemixException("unknown color : '{$background_color}'");
        }

        $left = "\033[" . self::TEXT_COLORS[$text_color] . "m";
        if ($background_color) {
            $left .= "\033[" . self::BACKGROUND_COLORS[$background_color] . "m";
        }
        $right = "\033[0m";
        return $left . $text . $right;
    }
    // function color()
}
// class Effector
