<?php

namespace Remix;

class Config extends Component
{
    protected $dir = '';
    protected $hash = null;

    public function __construct()
    {
        \Remix\App::getInstance()->log(__METHOD__);

        parent::__construct();
        $remix = App::getInstance();
        $this->dir = $remix->appDir('/config');

        $this->hash = $remix->factory(Hash::class);
    } // function __construct()

    public function __destruct()
    {
        \Remix\App::getInstance()->log(__METHOD__);
    }

    public function load(string $file, string $key = '')
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
        //return $this->config;
        return $this->hash->get($name);
    } // function get()
} // class Config
