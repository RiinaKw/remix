<?php

namespace Remix;

/**
 * Remix Track : web route definition
 */
class Track extends \Remix\Component
{
    protected $props = [];
    protected $fader = null;

    private function __construct(string $path)
    {
        \Remix\App::getInstance()->logBirth(__METHOD__ . ' [' . $path . ']');
    } // function __construct()

    public function __destruct()
    {
        \Remix\App::getInstance()->logDeath(__METHOD__ . ' [' . $this->path . ']');
    } // function __destruct()

    public static function get(string $path, $action)  : self
    {
        //'/^\/api\/(?<id>\S+?)(\.(?<ext>\S+?))?\/?$/'
        $track = new static($path);
        $track->props['method'] = 'get';
        $track->props['action'] = $action;
        $track->props['path'] = $path;
        return $track;
    } // function get()

    public function name(string $name) : self
    {
        $this->props['name'] = $name;
        return $this;
    }

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

    public function isMatch(string $path) : bool
    {
        //$result = preg_match($this->pattern, $path);
        $this->fader = \Remix\Fader::factory($this->props['path']);
        $result = $this->fader->isMatch($path);
        if (! $result) {
            $this->fader = null;
        }
        return $result;
    } // function isMatch()

    public function matched() : array
    {
        return $this->fader ? $this->fader->matched() : null;
    } // function isMatch()

    public function uri() : string
    {
        return App::getInstance()->preset()->get('env.public_url') . $this->path;
    } // function uri()
} // class Track
