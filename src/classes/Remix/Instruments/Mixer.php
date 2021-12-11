<?php

namespace Remix\Instruments;

// Remix core
use Remix\Instrument;
use Remix\Audio;
use Remix\Track;
use Remix\Fader;
use Remix\Channel;
use Remix\Sampler;
use Remix\Studio;
use Remix\Lyric;
// Exceptions
use Remix\RemixException;
use Remix\Exceptions\HttpException;
// Utilities
use Utility\Arr;
use Utility\Http\StatusCode;

/**
 * Remix Mixer : routing.
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Mixer extends Instrument
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
        $this->tracks = Arr::flatten($this->tracks);

        foreach ($this->tracks as $track) {
            $name = $track->name;
            if ($name) {
                if (isset($this->named[$name])) {
                    $message = 'Tracks cannot have the same named Track "' . $name . '"';
                    throw new RemixException($message);
                }
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

    public function __destruct()
    {
        $this->tracks = null;
        $this->named = null;
        $this->urls = null;
        parent::__destruct();
    }
    // function __destruct()

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function route(string $path): Studio
    {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        $uri = $_SERVER['PATH_INFO'] ?? $path;
        $method = $_REQUEST['_method'] ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $method = strtoupper($method);

        foreach ($this->urls as $url => $tracks) {
            // get track
            $fader = new Fader($url);
            if ($fader->isMatch($path)) {
                if (isset($tracks['ANY'])) {
                    $track = $tracks['ANY'];
                } elseif (isset($tracks[$method])) {
                    $track = $tracks[$method];
                } else {
                    $message = 'method ' . $method . ' not allowed, given ' . $method . ' ' . $path;
                    throw new HttpException($message, StatusCode::METHOD_NOT_ALLOWED);
                }

                // setup Studio
                $params = [
                    // 'path' => $path,
                    'method' => $method,
                    'data' => $fader->matched($path),
                ];
                $sampler = (new Sampler())->load($uri, $params);
                return static::studio($track->action, $sampler);
            }
        }
        $message = 'it did not match any route, given ' . $path;
        throw new HttpException($message, StatusCode::NOT_FOUND);
    }
    // function route()

    /**
     * Execute a method of the Channel.
     * Also call "before()" and "after()" methods if they exist.
     *
     * @param  Channel        $channel  Target channel
     * @param  string         $method   Method name of channel
     * @param  Sampler        $sampler  Input object
     * @return string|Studio
     *
     * @todo Need to be non-static?
     */
    protected static function play(Channel $channel, string $method, Sampler $sampler)
    {
        if (! method_exists($channel, $method)) {
            $class = get_class($channel);
            throw new RemixException(
                "Channel '{$class}' does not contain the method '{$method}'"
            );
        }

        if (method_exists($channel, 'before')) {
            $channel->before($sampler);
        }

        $result = $channel->$method($sampler);

        if (method_exists($channel, 'after')) {
            $result = $channel->after($sampler, $result);
        }
        return $result;
    }
    // function play()

    protected static function toChannel(string $classname, string $method): Channel
    {
        // Find a class by Channel name
        $namespace = Audio::getInstance()->preset->get('app.namespace');
        $class = "\\{$namespace}\\Channels\\{$classname}";

        if (! class_exists($class)) {
            throw new RemixException("Unknwon channel '{$class}'");
        }

        // The argument is to propagate to Delay
        return new $class($method);
    }

    protected static function studio($action, Sampler $sampler)
    {
        if (is_object($action)) {
            // In case of closure, return as it is
            return new Studio('closure', $action);
        }

        if (is_string($action) && strpos($action, '@')) {
            // Call the method of Channel if the string is separated by "@"
            list($class, $method) = explode('@', $action);
            $channel = static::toChannel($class, $method);
        } elseif (is_array($action) && is_callable($action)) {
            // When the method of the object is specified directly
            list($channel, $method) = $action;

            // Make sure it is not a static method
            if (! $channel instanceof Channel) {
                $class = Channel::class;
                throw new RemixException(
                    "The mixer definition must be an instance of the {$class}"
                );
            }
        } else {
            throw new RemixException("Unable to run mixer due to unknown action");
        }

        // execute the method of the Channel
        $result = static::play($channel, $method, $sampler);

        if ($result instanceof Studio) {
            return $result;
        } else {
            return new Studio('text', $result);
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
            throw new RemixException("track '{$name}' not found");
        }

        $path = $track->path;
        preg_match('/\(\/:(?<name>.+?)\)\?/', $path, $matches);
        if ($matches && isset($matches['name'])) {
            $source = $matches[0];
            $key = $matches['name'];

            $value = $params[$key] ?? null;
            if ($value !== null) {
                $path = str_replace($source, '/' . $value, $path);
            } else {
                $path = str_replace($source, '', $path);
            }
        }

        foreach ($params as $label => $value) {
            $path = str_replace($label, $value, $path);
        }
        return Lyric::getInstance()->sing($path);
    }
    // function uri()
}
// class Mixer
