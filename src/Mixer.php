<?php

namespace Remix;

/**
 * Remix Mixer : routing
 */
class Mixer extends Gear
{
    protected $tracks = [];
    protected $named = [];
    protected $urls = [];

    public function load($tracks): self
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

            $this->urlArr($track);
        }
        return $this;
    }
    // function load()

    protected function urlArr(Track $track)
    {
        $path = $track->path;
        if (! isset($this->urls[$path])) {
            $this->urls[$path] = [];
        }
        $this->urls[$path][$track->method] = $track;
    }

    public function destroy(): void
    {
        $this->tracks = null;
        $this->named = null;
        $this->urls = null;
    }
    // function destroy()

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function route(string $path): Studio
    {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        foreach ($this->urls as $url => $tracks) {
            $fader = \Remix\Fader::factory($url);

            // get track
            $result = $fader->isMatch($path);
            if ($result) {
                if (! isset($tracks[$method])) {
                    return Studio::factory()->header(405, 'method not allowed, given ' . $method . ' ' . $path);
                }

                // setup Studio
                $track = $tracks[$method];
                $sampler = Audio::getInstance()->equalizer->instance(Sampler::class, $fader->matched($path));
                return static::studio($track->action, $sampler);
            }
        }
        return Studio::factory()->header(404, 'it did not match any route, given ' . $path);
    }
    // function route()

    protected static function studio($action, Sampler $sampler)
    {
        if (is_string($action) && strpos($action, '@')) {
            list($class, $method) = explode('@', $action);
            $class = '\\App\\Channel\\' . $class;
            $channel = new $class();
            $action = [$channel, $method];
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
    // function studio()

    public function named(string $named)
    {
        return $this->named[$named] ?? null;
    }
    // function named()

    public function uri(string $name, array $params = [])
    {
        $track = $this->named($name);
        if (! $track) {
            throw new \Remix\RemixException('track "' . $name . '" not found');
        }

        $path = $track->path;
        foreach ($params as $label => $value) {
            $path = str_replace($label, $value, $path);
        }

        $public_url = Audio::getInstance()->preset->get('env.public_url');
        return $public_url . $path;
    }
    // function uri()
}
// class Mixer
