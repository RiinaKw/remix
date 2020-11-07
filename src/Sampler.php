<?php

namespace Remix;

/**
 * Remix Sampler : web input manager
 */
class Sampler extends Component
{
    protected $param = null;
    protected $get = null;
    protected $post = null;

    public function __construct(array $param)
    {
        $this->param = new Utility\Hash;
        foreach ($param as $key => $item) {
            if (! is_int($key)) {
                $this->param->set($key, $item);
            }
        }

        $this->get = new Utility\Hash($_GET);
        $this->post = new Utility\Hash($_POST);
    } // function __construct()

    public function param(string $name = '')
    {
        return $this->param->get($name);
    } // function param()

    public function get(string $name = '')
    {
        return $this->get->get($name);
    } // function get()

    public function post(string $name = '')
    {
        return $this->post->get($name);
    } // function post()
} // class Sampler
