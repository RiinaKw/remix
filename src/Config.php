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
        $this->hash = new Utility\Hash;
    } // function __construct()

    public function load(string $file, string $key = '') : void
    {
        $filename = str_replace('.', '/', $file);
        $file = $this->dir . '/' . $filename . '.php';

        if (! realpath($file)) {
            throw new RemixException('config file not found');
        }

        $config = require($file);
        if ($key) {
            $this->hash->set($key, $config);
        } else {
            $this->hash->set($filename, $config);
        }
    } // function load()

    public function get(string $name = '')
    {
        return $this->hash->get($name);
    } // function get()

    public function set(string $name, $value)
    {
        return $this->hash->set($name, $value);
    } // function get()

    public function env() : string
    {
        return $this->hash->get('env.name');
    }
} // class Config
