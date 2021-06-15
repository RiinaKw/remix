<?php

namespace Remix;

use Utility\Hash;
use Remix\Exceptions\CoreException;

/**
 * Remix Preset : configs manager
 *
 * @package  Remix\Core
 */
class Preset extends Gear
{
    /**
     * Is it required or optional?
     * @var bool
     * @todo Can't I make it an enum?
     */
    private const REQUIRED = true;

    /**
     * Is it required or optional?
     * @var bool
     * @todo Can't I make it an enum?
     */
    private const OPTIONAL = false;

    /**
     * Is it replace or append?
     * @var bool
     * @todo Can't I make it an enum?
     */
    public const APPEND = true;

    /**
     * Is it replace or append?
     * @var bool
     * @todo Can't I make it an enum?
     */
    public const REPLACE = false;

    /**
     * Remix core directory
     * @var string
     */
    private $remix_dir = '';

    /**
     * Application directory
     * @var string
     * @todo I wanna rename to '$app_dir'.
     */
    private $dir = '';

    /**
     * All settings it's managing
     * @var Hash
     */
    private $hash = null;

    /**
     * Create internal Hash.
     */
    public function __construct()
    {
        parent::__construct();
        $this->hash = new Hash();
    }
    // function __construct()

    /**
     * Set Remix core directory.
     *
     * @param  string $dir  Remix core directory
     * @return self         itself
     */
    public function remixDir(string $dir): self
    {
        $this->remix_dir = $dir;
        return $this;
    }
    // function remixDir()

    /**
     * Set application directory.
     *
     * @param  string $dir  Application directory.
     * @return self         itself
     */
    public function appDir(string $dir): self
    {
        $this->dir = $dir;
        return $this;
    }
    // function appDir()

    /**
     * Load the required configs in the Remix namespace.
     *
     * @param  string $file    Target file
     * @param  string $key     Key of the hash to manage config
     * @param  bool   $append  Is it replace or append?
     */
    public function remixRequire(string $file, string $key = '', bool $append = false): void
    {
        $this->load('remix', static::REQUIRED, $file, $key, $append);
    }

    /**
     * Load the required configs in the application namespace.
     *
     * @param  string $file    Target file
     * @param  string $key     Key of the hash to manage config
     * @param  bool   $append  Is it replace or append?
     */
    public function require(string $file, string $key = '', bool $append = false): void
    {
        $this->load('app', static::REQUIRED, $file, $key, $append);
    }

    /**
     * Load the optional configs in the application namespace.
     *
     * @param  string $file    Target file
     * @param  string $key     Key of the hash to manage config
     * @param  bool   $append  Is it replace or append?
     */
    public function optional(string $file, string $key = '', bool $append = false): void
    {
        $this->load('app', static::OPTIONAL, $file, $key, $append);
    }

    /**
     * Load config file.
     *
     * @param string $namespace  Remix or Application
     * @param bool   $required   Is it required? (If not required, ignore non-existent files)
     * @param string $file       Target file
     * @param string $key        Key of the hash to manage config
     * @param bool   $append     Is it replace or append?
     * @throws RemixException When required and the target file is not found
     */
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

    /**
     * Get from the config hash.
     *
     * @param  string $name     Key of hash
     * @param  mixed  $default  Default value if the key does not exist.
     * @return mixed            Value of config
     */
    public function get(string $name = '', $default = null)
    {
        return $this->hash->get($name) ?: $default;
    }
    // function get()

    /**
     * Set to the config hash.
     *
     * @param string $name   Key of hash
     * @param mixed  $value  The value to register
     */
    public function set(string $name, $value)
    {
        return $this->hash->set($name, $value);
    }
    // function get()

    /**
     * Get current environment
     *
     * @return string  Current environment
     */
    public function env(): string
    {
        return $this->hash->get('env.name');
    }
    // function env()
}
// class Preset
