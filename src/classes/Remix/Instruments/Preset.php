<?php

namespace Remix\Instruments;

use Remix\Instrument;
use Remix\Instruments\PresetLoader;
use Utility\Hash;
use Remix\Exceptions\CoreException;

/**
 * Remix Preset : configs manager
 *
 * @package  Remix\Core
 */
class Preset extends Instrument
{
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

    public function __destruct()
    {
        $this->hash->truncate();
        $this->hash = null;

        parent::__destruct();
    }

    /**
     * Set Remix core directory.
     *
     * @param  string $dir  Remix core directory
     * @return self         itself
     */
    public function remixDir(string $dir): self
    {
        PresetLoader::directory('remix', $dir);
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
        PresetLoader::directory('app', $dir);
        return $this;
    }
    // function appDir()

    /**
     * Load the preset file.
     * @param  PresetLoader $loader  Loader instance
     * @param  string|null  $target  Target key of hash, or default preset name if null
     */
    public function load(PresetLoader $loader, string $target = null): void
    {
        if (! $loader->exists()) {
            if ($loader->required) {
                throw new CoreException("preset file '{$loader->preset}' not found");
            }
            // If not required, do nothing
            return;
        }
        $preset = $loader->load();

        // Decide the key of hash
        /**
         * @todo Can I make more simple?
         */
        if ($target === $loader->namespace) {
            $key = $target;
        } elseif ($loader->namespace !== $loader->preset) {
            $key = $loader->namespace . '.' . $loader->preset;
        } else {
            $key = $loader->preset;
        }

        if ($loader->replace) {
            // Replace the hash item
            $this->hash->set($key, $preset);
        } else {
            // Append to the hash item
            $this->hash->pushHash($key, $preset);
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
