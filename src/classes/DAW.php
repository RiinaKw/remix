<?php

namespace Remix;

/**
 * Remix DAW : entry point
 */
class DAW extends Gear
{
    protected $remix_dir;
    protected $app_dir;
    protected $public_dir;

    public static function factory($arg1 = null, $arg2 = null): self
    {
        if ($arg1 === null) {
            return new static();
        } elseif ($arg2 === null) {
            return new static($arg1);
        } else {
            return new static($arg1, $arg2);
        }
    }

    public function initialize(string $dir): self
    {
        $this->app_dir = realpath($dir);
        $this->remix_dir = realpath(__DIR__ . '/..');

        $env = require($this->appdir('env.php'));
        $env = ($env && $env !== 1) ? $env : 'production';

        $preset = Audio::getInstance()->preset;
        $preset->set('remix.root_dir', $this->remix_dir);
        $preset->set('remix.bounce_dir', '/bounces');
        $preset->load('app', 'app');
        $preset->load('env.' . $env, 'app', true);

        Audio::getInstance()->dj;
        return $this;
    }
    // function initialize()

    public function remixDir(string $path): string
    {
        return realpath($this->remix_dir . '/' . $path);
    }
    // function dir()

    public function appDir(string $path = ''): string
    {
        return realpath($this->app_dir . '/' . $path);
    }
    // function appDir()

    public function publicDir(string $path = ''): string
    {
        return realpath($this->public_dir . '/' . $path);
    }
    // function publicDir()

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function playWeb(string $public_dir): Studio
    {
        $audio = Audio::getInstance();
        $audio->cli = false;

        $this->public_dir = $public_dir;
        $path = $_SERVER['PATH_INFO'] ?? '';

        $tracks_path = $this->appDir('/mixer.php') ?: [];
        $mixer = $audio->mixer;
        $studio = $mixer->load($tracks_path)->route($path);
        Delay::log(true, 'BODY', '');
        $mixer->destroy();
        $mixer = null;
        $audio = null;
        return $studio;
    }
    // function playWeb()

    public function playCli(array $argv): void
    {
        $audio = Audio::getInstance();
        $audio->amp->play($argv);
        Delay::log(true, 'BODY', '');
        $audio = null;
    }
    // function playCli()
}
// class DAW
