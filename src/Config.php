<?php

namespace Remix;

class Config extends Component
{
    protected $dir = '';
    protected $hash = null;

    public function __construct()
    {
        parent::__construct();
        $remix = App::getInstance();
        $this->dir = $remix->appDir('/config');

        $this->hash = $remix->factory(Hash::class);
    } // function __construct()

    public function load(string $name)
    {
        $file = $this->dir . '/' . $name . '.php';
        if (! realpath($file)) {
            throw new RemixException('config file not found');
        }

        $config = require($file);
        $this->hash->set($name, $config);
    } // function load()

    public function get(string $name)
    {
        //return $this->config;
        return $this->hash->get($name);
    } // function get()
} // class Config
