<?php

namespace Remix;

use Utility\Hash\ReadOnlyHash;
use Utility\Http;

/**
 * Remix Sampler : web input manager
 *
 * @package  Remix\Web
 * @todo Write the details.
 */
class Sampler extends Gear
{
    protected $params_hash = null;
    protected $get_hash = null;
    protected $post_hash = null;
    protected $session_hash = null;

    protected $method = '';
    protected $uri = '';

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function load(string $uri, array $params)
    {
        $input = [];
        foreach ($params['data'] as $key => $item) {
            if (! is_int($key)) {
                $input[$key] = $item;
            }
        }
        foreach ($_REQUEST as $key => $item) {
            if (! is_int($key) && $key !== '_method') {
                $input[$key] = $item;
            }
        }
        $this->params_hash = new ReadOnlyHash($input);

        $this->method = $params['method'];
        $this->uri = $uri;

        $this->get_hash = Http\GetHash::factory();
        $this->post_hash = Http\PostHash::factory();
        $this->session_hash = Http\SessionHash::factory();
    }
    // function load()

    public function params(string $name = '', $default = null)
    {
        return $this->params_hash->get($name) ?? $default;
    }
    // function param()

    public function get(string $name = '', $default = null)
    {
        return $this->get_hash->get($name) ?? $default;
    }
    // function get()

    public function post(string $name = '', $default = null)
    {
        return $this->post_hash->get($name) ?? $default;
    }
    // function post()

    public function session(): Http\SessionHash
    {
        return $this->session_hash;
    }

    public function method(): string
    {
        return $this->method;
    }
    // function method()

    public function uri(): string
    {
        return $this->uri;
    }
    // function uri()
}
// class Sampler
