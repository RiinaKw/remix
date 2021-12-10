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
            $self->base_uri = rtrim($preset->get('app.public_url'), '/');
        }
        return $self;
    }

    public function make(string $path): string
    {
        return $this->base_uri . '/' . ltrim($path, '/');
    }

    public function named(string $name): string
    {
        $track = Audio::getInstance()->mixer->named($name);
        if (! $track) {
            return null;
        }

        return $this->make($track->path);
    }
}
// class Lyric
