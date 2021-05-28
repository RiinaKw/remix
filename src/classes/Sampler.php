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

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function __construct(array $param)
    {
        parent::__construct();

        $this->params_hash = new Hash();
        foreach ($param as $key => $item) {
            if (! is_int($key)) {
                $this->params_hash->set($key, $item);
            }
        }

        $this->get_hash = new Hash($_GET);
        $this->post_hash = new Hash($_POST);
    }
    // function __construct()

    public function param(string $name = '')
    {
        return $this->params_hash->get($name);
    }
    // function param()

    public function get(string $name = '')
    {
        return $this->get_hash->get($name);
    }
    // function get()

    public function post(string $name = '')
    {
        return $this->post_hash->get($name);
    }
    // function post()
}
// class Sampler
