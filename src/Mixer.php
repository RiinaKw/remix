<?php

namespace Remix;

/**
 * Remix Mixer : routing
 */
class Mixer extends Component
{
    protected $tracks = [];
    protected $named = [];

    protected function __construct()
    {
        parent::__construct();
        \Remix\App::getInstance()->logBirth(__METHOD__);
    } // function __construct()

    public function __destruct()
    {
        \Remix\App::getInstance()->logDeath(__METHOD__);
        parent::__destruct();
    } // function __destruct()

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
    } // function load()

    public function destroy()
    {
        $this->tracks = null;
        $this->named = null;
    }

    public function route(string $path) : Studio
    {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        foreach ($this->tracks as $track) {
            if ($track->match($path)) {
                $sampler = $track->sampler($path);
                $studio = $track->call($sampler);
                unset($track);
                return $studio;
            }
        }
        throw new Exceptions\HttpException('it did not match any route, given ' . $path, 404);
    } // function route()

    public function named(string $named)
    {
        return $this->named[$named] ?? null;
    } // function named()
} // class Mixer
