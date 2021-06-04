<?php

namespace Remix;

class Preset extends Gear
{
    private const REQUIRED = true;
    private const OPTIONAL = false;

    private $dir = '';
    private $hash = null;

    public function __construct()
    {
        parent::__construct();
        $this->dir = Audio::getInstance()->daw->appDir('/presets');
        $this->hash = new Utility\Hash();
    }
    // function __construct()

    public function require(string $file, string $key = '', bool $append = false)
    {
        $this->load(static::REQUIRED, $file, $key, $append);
    }

    public function optional(string $file, string $key = '', bool $append = false)
    {
        $this->load(static::OPTIONAL, $file, $key, $append);
    }

    private function load(bool $required, string $file, string $key = '', bool $append = false): void
    {
        $filename = str_replace('.', '/', $file);
        $file = $this->dir . '/' . $filename . '.php';
        $daw = null;

        if (! realpath($file)) {
            if ($required === static::REQUIRED) {
                throw new RemixException("preset file '{$filename}' not found");
            } else {
                return;
            }
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

    public function get(string $name = '', $default = null)
    {
        return $this->hash->get($name) ?: $default;
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
