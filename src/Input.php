<?php

namespace Remix;

class Input extends Component
{

    protected $param = null;

    public function __construct(array $param)
    {
        $this->param = new \Remix\Hash;
        foreach ($param as $key => $item) {
            if (! is_int($key)) {
                $this->param->set($key, $item);
            }
        }
    } // function __construct()

    public function get(string $name)
    {
        return $this->param->get($name);
    } // function get()
} // class Input
