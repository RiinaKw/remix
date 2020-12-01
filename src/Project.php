<?php

namespace Remix;

/**
 * Remix Project : entry point
 */
class Project extends Component
{
    protected $root_dir;
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
        $this->root_dir = realpath($dir);
        $this->app_dir = $this->dir('app');

        $env = require($this->appdir('env.php'));
        $env = ($env && $env !== 1) ? $env : 'production';

        $preset = App::getInstance()->preset;
        $preset->set('env.name', $env);
        $preset->load('app');
        $preset->load('env.' . $env, 'env');

        App::getInstance()->dj;
        return $this;
    }
    // function initialize()

    public function dir(string $path): string
    {
        return realpath($this->root_dir . '/' . $path);
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
    public function runWeb(string $public_dir): Studio
    {
        $app = App::getInstance();

        $this->public_dir = $public_dir;
        $path = $_SERVER['PATH_INFO'] ?? '';

        $tracks_path = $this->appDir('/mixer.php') ?: [];
        $mixer = $app->mixer;
        $studio = $mixer->load($tracks_path)->route($path);
        Delay::log(true, 'BODY', '');
        $mixer->destroy();
        $mixer = null;
        $app = null;
        return $studio;
    }
    // function runWeb()

    public function runCli(array $argv): void
    {
        $app = App::getInstance();
        $app->amp->run($argv);
        Delay::log(true, 'BODY', '');
        $app = null;
    }
    // function runCli()
}
// class Project
