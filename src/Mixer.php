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
        $this->tracks = Utility\Arr::flatten($this->tracks);

        foreach ($this->tracks as $track) {
            $name = $track->name;
            if ($name) {
                $this->named[$name] = $track;
            }
        }
        return $this;
    } // function load()

    public function destroy() : void
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
        //var_dump($track);
            if ($track->isMatch($path)) {
                //$sampler = $track->sampler($path);
                //$studio = $track->call($sampler);

                $equalizer = \Remix\App::getInstance()->equalizer();
                $sampler = $equalizer->instance(Sampler::class, $track->matched($path));

                $action = $track->action;
                if (is_string($action) && strpos($action, '@')) {
                    list($class, $method) = explode('@', $action);
                    $class = '\\App\\Channel\\' . $class;
                    $channel = new $class;
                    $action = [$channel, $method];
                    $this->action = $action;
                }
                if (is_object($action)) {
                    return Studio::factory('closure', $action);
                } elseif (is_callable($action)) {
                    $result = $action($sampler);
                    unset($sampler);
                    if ($result instanceof Studio) {
                        return $result;
                    } else {
                        return Studio::factory('text', $result);
                    }
                }
            }
        }
        throw new Exceptions\HttpException('it did not match any route, given ' . $path, 404);
    } // function route()

    public function named(string $named)
    {
        return $this->named[$named] ?? null;
    } // function named()
} // class Mixer
