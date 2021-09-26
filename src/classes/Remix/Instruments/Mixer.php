<?php

namespace Remix\Instruments;

use Remix\Instrument;
use Remix\Audio;
use Remix\Track;
use Remix\Fader;
use Remix\Sampler;
use Remix\Studio;
use Remix\RemixException;
use Remix\Exceptions\HttpException;
use Utility\Arr;

/**
 * Remix Mixer : routing
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
                    throw new HttpException($message, 405);
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
        throw new HttpException($message, 404);
    }
    // function route()

    protected static function studio($action, Sampler $sampler)
    {
        if (is_object($action)) {
            return new Studio('closure', $action);
        } else {
            if (is_string($action) && strpos($action, '@')) {
                list($class, $method) = explode('@', $action);


                $namespace = Audio::getInstance()->preset->get('app.namespace');
                $class = '\\' . $namespace . '\\Channels\\' . $class;

                if (! class_exists($class)) {
                    throw new RemixException('Unknwon channel "' . $class . '"');
                }

                $channel = new $class($method);
            } elseif (is_callable($action)) {
                list($channel, $method) = $action;
            }
            $result = $channel->play($method, $sampler);

            if ($result instanceof Studio) {
                return $result;
            } else {
                return new Studio('text', $result);
            }
            return $result;
        }
        throw new RemixException(__METHOD__ . ' has some errors??');
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
            throw new RemixException('track "' . $name . '" not found');
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

        $public_url = Audio::getInstance()->preset->get('app.public_url');
        return $public_url . $path;
    }
    // function uri()
}
// class Mixer
