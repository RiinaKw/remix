<?php

namespace Remix;

/**
 * Remix Track : web route definition
 */
class Track extends \Remix\Component
{

    protected $pattern = '';
    protected $match = [];
    protected $action = null;
    protected $input = null;

    protected function makePattern(string $path)
    {
        $pattern = str_replace('/', '\/', $path);
        $pattern = preg_replace('/:([^\/]+)/', '(?<$1>[^\/]+)', $pattern);
        return '/^' . $pattern . '\/?$/';
    } // function makePattern()

    public static function get(string $path, $action)
    {
        $track = new static;
        $track->action = $action;
        $track->pattern = $track->makePattern($path);
        return $track;
    } // function get()

    public function match(string $path)
    {
        $result = preg_match($this->pattern, $path, $match);
        $this->input = new \Remix\Input($match);
        return $result;
    } // function match()

    public function call()
    {
        $action = $this->action;
        if (is_callable($action)) {
            $action($this->input);
        } elseif (strpos($action, '@')) {
            list($class, $method) = explode('@', $action);
            $class = '\\App\\Controller\\' . $class;
            $controller = new $class;
            $controller->$method($this->input);
        }
    } // function call()
} // class Track
