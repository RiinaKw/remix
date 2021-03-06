<?php

namespace Remix;

class Preset extends Gear
{
    //protected $dir = '';
    protected $hash = null;

    public function __construct()
    {
        parent::__construct();
        $this->hash = new Utility\Hash();
    }
    // function __construct()

    public function load(string $file, string $key = '', bool $append = false): void
    {
        $filename = str_replace('.', '/', $file);
        $daw = Audio::getInstance()->daw;
        $file = $daw->appDir('/presets') . '/' . $filename . '.php';
        $daw = null;

        if (! realpath($file)) {
            throw new RemixException("preset file '{$filename}' not found");
        }

        if (! $key) {
            $key = $filename;
        }
        $preset = require($file);

        if ($append) {
            $this->hash->pushHash($key, $preset);
        } else {
            $this->hash->set($key, $preset);
        }
    }
    // function load()

    public function get(string $name = '')
    {
        return $this->hash->get($name);
    }
    // function get()

    public function set(string $name, $value)
    {
        return $this->hash->set($name, $value);
    }
    // function get()

    public function env(): string
    {
        return $this->hash->get('env.name');
    }
    // function env()
}
// class Preset
