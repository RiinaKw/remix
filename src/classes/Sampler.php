<?php

namespace Remix;

use Remix\Utility\Hash;

/**
 * Remix Sampler : web input manager
 */
class Sampler extends Gear
{
    protected $params_hash = null;
    protected $get_hash = null;
    protected $post_hash = null;
    protected $method = '';
    protected $uri = '';

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function load(string $uri, array $params)
    {
        $this->params_hash = new Hash();
        foreach ($params['data'] as $key => $item) {
            if (! is_int($key)) {
                $this->params_hash->set($key, $item);
            }
        }
        foreach ($_REQUEST as $key => $item) {
            if (! is_int($key) && $key !== '_method') {
                $this->params_hash->set($key, $item);
            }
        }

        $this->method = $params['method'];
        $this->uri = $uri;

        $this->get_hash = new Hash($_GET);
        $this->post_hash = new Hash($_POST);
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
