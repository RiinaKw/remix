<?php

namespace Remix;

/**
 * Remix Track : web route definition
 *
 * @package  Remix\Web
 */
class Track extends Gear
{
    protected $props = [];

    private function __construct(string $method, string $path, $action)
    {
        Delay::logBirth(static::class . ' [' . $method . ' ' . $path . ']');

        $this->props['method'] = $method;
        $this->props['path'] = $path;
        $this->props['action'] = $action;
    }
    // function __construct()

    public function __destruct()
    {
        Delay::logDeath(static::class . ' [' . $this->method . ' ' . $this->path . ']');
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

    public static function put(string $path, $action): self
    {
        $track = new static('PUT', $path, $action);
        return $track;
    }
    // function put()

    public static function delete(string $path, $action): self
    {
        $track = new static('DELETE', $path, $action);
        return $track;
    }
    // function put()

    public static function any(string $path, $action): self
    {
        $track = new static('ANY', $path, $action);
        return $track;
    }
    // function any()

    public function name(string $name): self
    {
        $this->props['name'] = $name;
        return $this;
    }
    // function name()

    public function api(): self
    {
        $this->props['path'] .= '(\.:ext)?';
        return $this;
    }
    // function api()

    public function __get(string $name)
    {
        switch ($name) {
            case 'action':
            case 'path':
            case 'method':
            case 'name':
                return $this->props[$name] ?? null;
            default:
                return null;
        }
    }
    // function __get()

    public function uri(): string
    {
        return Audio::getInstance()->preset()->get('env.public_url') . $this->path;
    }
    // function uri()
}
// class Track
