<?php

namespace Remix;

class Preset extends Component
{
    protected $dir = '';
    protected $hash = null;

    public function __construct()
    {
        parent::__construct();

        $remix = App::getInstance();
        $this->dir = $remix->appDir('/Preset');
        $this->hash = new Utility\Hash;
    } // function __construct()

    public function load(string $file, string $key = '') : void
    {
        $filename = str_replace('.', '/', $file);
        $file = $this->dir . '/' . $filename . '.php';

        if (! realpath($file)) {
            throw new RemixException('preset file not found');
        }

        $preset = require($file);
        if ($key) {
            $this->hash->set($key, $preset);
        } else {
            $this->hash->set($filename, $preset);
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
} // class Preset