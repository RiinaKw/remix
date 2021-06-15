<?php

namespace Remix;

/**
 * Remix DAW : entry point
 *
 * @package  Remix\Core
 * @todo Write the details.
 */
class DAW extends Gear
{
    protected $remix_dir;
    protected $app_dir;

    public function initializeCore(): self
    {
        $this->remix_dir = realpath(__DIR__ . '/../..');

        $preset = Audio::getInstance()->preset;
        $preset->remixDir($this->remixDir('/presets'));

        $preset->set('remix.pathes.root_dir', $this->remix_dir);

        $preset->remixRequire('versions', 'remix', Preset::APPEND);
        $preset->remixRequire('pathes', '', Preset::APPEND);
        foreach ($preset->get('remix.pathes') as $key => $value) {
            if ($key === 'root_dir') {
                continue;
            }
            $key = 'remix.pathes.' . $key;
            $preset->set($key, $this->remixDir($value));
        }

        return $this;
    }

    public function initializeApp(string $dir): self
    {
        $this->app_dir = realpath($dir);

        $env = require($this->appdir('env.php'));
        $env = ($env && $env !== 1) ? $env : 'production';

        $preset = Audio::getInstance()->preset;
        $preset->appDir($this->appDir('/presets'));

        $env_file = 'env.' . $env;
        $preset->require('app', 'app');
        $preset->require($env_file, 'app', Preset::APPEND);
        $preset->optional('effector');

        $bounce_dir = $preset->get('app.pathes.bounce_dir');
        $preset->set('app.pathes.bounce_dir', $this->appdir($bounce_dir));

        Audio::getInstance()->dj;
        return $this;
    }
    // function initializeApp()

    public function initialize(string $dir): self
    {
        return $this->initializeCore()->initializeApp($dir);
    }

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
