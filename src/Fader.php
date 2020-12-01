<?php

namespace Remix;

class Fader extends Component
{
    protected $pattern;
    protected $translated = '';
    protected $matches;

    protected function __construct(string $pattern)
    {
        Delay::logBirth(__METHOD__ . ' [' . $pattern . ']');

        $this->pattern = $pattern;
        $this->translated = static::translate($pattern);
    }
    // function __construct()

    public function __destruct()
    {
        Delay::logDeath(__METHOD__ . ' [' . $this->pattern . ']');
    }
    // function __destruct()

    protected static function translate(string $pattern): string
    {
        $pattern = str_replace('/', '\\/', $pattern);
        return '/^' . preg_replace('/:([a-zA-Z0-9]+)/', '(?<$1>\S+?)', $pattern) . '\/?$/';
    }
    // function translate()

    public function isMatch(string $string): bool
    {
        preg_match($this->translated, $string, $this->matches);
        return (bool)$this->matches;
    }
    // function isMatch()

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
