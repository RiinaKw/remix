<?php

namespace Remix;

class Lyric extends Gear
{
    private $base_uri = null;

    public static function getInstance(): self
    {
        $self = Audio::getInstance()->equalizer->singleton(static::class);
        if (! $self->base_uri) {
            $preset = Audio::getInstance()->preset;
            $self->base_uri = rtrim($preset->get('app.public_uri'), '/');
        }
        return $self;
    }

    public function make(string $path): string
    {
        return $this->base_uri . '/' . ltrim($path, '/');
    }

    public function named(string $name, array $params = []): string
    {
        return Audio::getInstance()->mixer->uri($name, $params);
    }
}
// class Lyric
