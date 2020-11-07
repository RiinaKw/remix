<?php

namespace Remix;

/**
 * Remix Mixer : routing
 */
class Mixer extends Component
{
    protected $tracks = [];
    protected $named = [];

    public function load($tracks) : self
    {
        if (is_string($tracks)) {
            $this->tracks = require($tracks);
        } else {
            $this->tracks = $tracks;
        }

        foreach ($this->tracks as $track) {
            $name = $track->name;
            if ($name) {
                $this->named[$name] = $track;
            }
        }
        return $this;
    }

    public function route(string $path) : Studio
    {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        foreach ($this->tracks as $track) {
            if ($track->match($path)) {
                $sampler = $track->sampler($path);
                return $track->call($sampler);
            }
        }
        throw new Exceptions\HttpException('it did not match any route, given ' . $path, 404);
    } // function route()

    public function named(string $named)
    {
        return $this->named[$named] ?? null;
    }
} // class Mixer
