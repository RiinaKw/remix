<?php

namespace Remix;

/**
 * Remix DAW : entry point
 */
class DAW extends Gear
{
    protected $remix_dir;
    protected $app_dir;

    public function initialize(string $dir): self
    {
        $this->app_dir = realpath($dir);
        $this->remix_dir = realpath(__DIR__ . '/..');

        $env = require($this->appdir('env.php'));
        $env = ($env && $env !== 1) ? $env : 'production';

        $preset = Audio::getInstance()->preset;
        $preset->set('remix.root_dir', $this->remix_dir);
        $preset->set('remix.bounce_dir', $this->remixDir('bounces'));
        $preset->set('remix.effector_dir', $this->remixDir('classes/Effector'));
        $preset->set('remix.exception_path', $this->remixDir('bounces/exception.tpl'));
        $preset->set('remix.console_path', $this->remixDir('bounces/console.tpl'));

        $preset->load('app', 'app');
        $preset->load('env.' . $env, 'app', true);
        $preset->load('effector', 'app.effector', true);

        $bounce_dir = $preset->get('app.bounce_dir');
        $preset->set('app.bounce_dir', $this->appdir($bounce_dir));

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

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function playWeb(): Studio
    {
        $audio = Audio::getInstance();
        $audio->cli = false;

        $path = $_SERVER['PATH_INFO'] ?? '';

        $tracks_path = $this->appDir('/mixer.php') ?: [];
        $mixer = $audio->mixer;
        $studio = $mixer->load($tracks_path)->route($path);
        Delay::log('BODY', $studio->getMimeType());
        $mixer->destroy();
        $mixer = null;
        $audio = null;
        return $studio;
    }
    // function playWeb()

    public function playCli(array $argv): void
    {
        $audio = Audio::getInstance();
        $audio->amp->initialize()->play($argv);
        Delay::log('BODY', '');
        $audio = null;
    }
    // function playCli()
}
// class DAW
