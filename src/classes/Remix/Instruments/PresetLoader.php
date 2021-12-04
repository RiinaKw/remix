<?php

namespace Remix\Instruments;

use Remix\Exceptions\CoreException;

class PresetLoader
{
    private static $directories = [];

    private $preset;
    private $namespace;
    private $realfile;
    private $required = false;
    private $replace = false;

    public static function directory(string $namespace, string $dir)
    {
        static::$directories[$namespace] = $dir;
    }

    public function __construct(string $preset_name)
    {
        $this->preset = $preset_name;
    }

    public function namespace(string $namespace): self
    {
        $dir = static::$directories[$namespace] ?? null;
        if (! $dir) {
            throw new CoreException("unknwon namespace '{$namespace}'");
        }
        $this->namespace = $namespace;

        $filename = str_replace('.', '/', $this->preset);
        $this->realfile = $dir . '/' . $filename . '.php';

        return $this;
    }

    public function required(): self
    {
        $this->required = true;
        return $this;
    }

    public function replace(): self
    {
        $this->replace = true;
        return $this;
    }

    public function __get(string $name)
    {
        switch ($name) {
            case 'preset':
                return $this->preset;
            case 'namespace':
                return $this->namespace;
            case 'required':
                return $this->required;
            case 'replace':
                return $this->replace;
            default:
                throw new CoreException("PresetLoader : unknwon key '{$name}'");
        }
    }

    public function exists(): bool
    {
        return (bool)realpath($this->realfile);
    }

    public function load(): array
    {
        if (! $this->exists()) {
            return [];
        }
        return require($this->realfile);
    }
}
