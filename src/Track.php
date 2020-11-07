<?php

namespace Remix;

/**
 * Remix Track : web route definition
 */
class Track extends \Remix\Component
{
    protected $pattern = '';
    protected $path = '';
    protected $match = [];
    protected $action = null;
    protected $name = '';

    protected static function makePattern(string $path) : string
    {
        return '@^' . preg_replace('/:([^\/]+)/', '(?<$1>[^\/]+)', $path) . '/?$@';
    } // function makePattern()

    public static function get(string $path, $action)  : Track
    {
        $track = new static;
        $track->action = $action;
        $track->path = $path;
        $track->pattern = static::makePattern($path);
        return $track;
    } // function get()

    public function name(string $name) : Track
    {
        $this->name = $name;
        return $this;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'name':
                return $this->name;
            default:
                return null;
        }
    }

    public function match(string $path) : bool
    {
        $result = preg_match($this->pattern, $path);
        return $result;
    } // function match()

    public function sampler(string $path) : Sampler
    {
        $result = preg_match($this->pattern, $path, $match);
        return new Sampler($match);
    } // function sampler()

    public function call(Sampler $sampler) : Studio
    {
        $action = $this->action;
        if (is_string($action) && strpos($action, '@')) {
            list($class, $method) = explode('@', $action);
            $class = '\\App\\Channel\\' . $class;
            $channel = new $class;
            $action = [$channel, $method];
            $this->action = $action;
        }

        if (is_object($action)) {
            return new Studio('closure', $action);
        } elseif (is_callable($action)) {
            return $action($sampler);
        }
        return new Studio;
    } // function call()

    public function uri() : string
    {
        return App::getInstance()->config()->get('app.public_url') . $this->path;
    } // function uri()
} // class Track
