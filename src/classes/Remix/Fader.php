<?php

namespace Remix;

/**
 * Remix Fader : regular expression manager
 *
 * @package  Remix\Core
 */
class Fader extends Gear
{
    /**
     * Source pattern.
     * @var string
     */
    protected $pattern;

    /**
     * Translated regular expressions.
     * @var string
     */
    protected $translated = '';

    /**
     * Matched parameters.
     * @var array
     */
    protected $matches = [];

    /**
     * Let Delay know that an instance has been constructed and the pattern it is holding.
     *
     * @param string $pattern  Pattern of expression.
     */
    public function __construct(string $pattern)
    {
        parent::__construct($pattern);

        $this->pattern = $pattern;
        $this->translated = static::translate($pattern);
    }
    // function __construct()

    /**
     * Translates a pattern to a regular expression.
     *
     * @param  string $pattern  Source pattern.
     * @return string           Translated regular expressions.
     */
    protected static function translate(string $pattern): string
    {
        $pattern = str_replace('/', '\\/', $pattern);
        return '/^' . preg_replace('/:([a-zA-Z0-9]+)/', '(?<$1>\S+?)', $pattern) . '\/?$/';
    }
    // function translate()

    /**
     * Does it match the regular expression it has?
     *
     * @param  string $string  Original string
     * @return bool            Match or not
     */
    public function isMatch(string $string): bool
    {
        preg_match($this->translated, $string, $this->matches);
        return (bool)$this->matches;
    }
    // function isMatch()

    /**
     * Return the matched part.
     *
     * @return array Matched parameters
     */
    public function matched(): array
    {
        $matches = $this->matches;
        foreach (array_keys($matches) as $key) {
            if (is_int($key)) {
                unset($matches[$key]);
            }
        }
        return $matches;
    }
    // function matched()
}
// class Fader
