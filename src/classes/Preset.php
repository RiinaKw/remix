<?php

namespace Remix;

class Preset extends Gear
{
    private const REQUIRED = true;
    private const OPTIONAL = false;

    public const APPEND = true;
    public const REPLACE = false;

    private $remix_dir = '';
    private $dir = '';
    private $hash = null;

    public function __construct()
    {
        parent::__construct();
        $this->remix_dir = Audio::getInstance()->daw->remixDir('/presets');
        $this->dir = Audio::getInstance()->daw->appDir('/presets');
        $this->hash = new Utility\Hash();
    }
    // function __construct()

    public function remixRequire(string $file, string $key = '', bool $append = false)
    {
        $this->load('remix', static::REQUIRED, $file, $key, $append);
    }

    public function require(string $file, string $key = '', bool $append = false)
    {
        $this->load('app', static::REQUIRED, $file, $key, $append);
    }

    public function optional(string $file, string $key = '', bool $append = false)
    {
        $this->load('app', static::OPTIONAL, $file, $key, $append);
    }

    private function load(string $namespace, bool $required, string $file, string $key = '', bool $append = false): void
    {
        $filename = str_replace('.', '/', $file);
        $file = ($namespace === 'remix' ? $this->remix_dir : $this->dir) . '/' . $filename . '.php';

        if (! realpath($file)) {
            if ($required === static::REQUIRED) {
                throw new RemixException("preset file '{$filename}' not found");
            } else {
                return;
            }
        }
        $preset = require($file);

        if ($namespace !== $key) {
            $key = $namespace . '.' . ($key ?: $filename);
        }
        if ($append == static::APPEND) {
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
