<?php

namespace Remix;

/**
 * Remix Track : web route definition
 */
class Track extends \Remix\Component
{
    protected $props = [];

    private function __construct(string $method, string $path, $action)
    {
        \Remix\App::getInstance()->logBirth(__METHOD__ . ' [' . $method . ' ' . $path . ']');

        $this->props['method'] = $method;
        $this->props['path'] = $path;
        $this->props['action'] = $action;
    }
    // function __construct()

    public function __destruct()
    {
        \Remix\App::getInstance()->logDeath(__METHOD__ . ' [' . $this->method . ' ' . $this->path . ']');
    }
    // function __destruct()

    public static function get(string $path, $action): self
    {
        $track = new static('GET', $path, $action);
        return $track;
    }
    // function get()

    public static function post(string $path, $action): self
    {
        $track = new static('POST', $path, $action);
        return $track;
    }
    // function post()

    public function name(string $name): self
    {
        $this->props['name'] = $name;
        return $this;
    }
    // function name()

    public function __get(string $name)
    {
        switch ($name) {
            case 'action':
            case 'path':
            case 'method':
                return $this->props[$name];
            case 'name':
                return $this->props[$name] ?? null;
            default:
                return null;
        }
    }
    // function __get()

    public function uri(): string
    {
        return App::getInstance()->preset()->get('env.public_url') . $this->path;
    }
    // function uri()
}
// class Track
