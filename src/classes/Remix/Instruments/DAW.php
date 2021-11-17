<?php

namespace Remix\Instruments;

use Remix\Instrument;
use Remix\Audio;
use Remix\Reverb;
use Remix\Delay;

/**
 * Remix DAW : entry point
 *
 * @package  Remix\Core
 * @todo Write the details.
 */
class DAW extends Instrument
{
    private $preset = null;
    private $reverb = null;

    protected $remix_dir;
    protected $app_dir;

    public function initializeCore(): self
    {
        $this->loadPreset();

        $this->remix_dir = realpath(__DIR__ . '/../../..');
        $this->preset->remixDir($this->remixDir('/presets'));

        $this->preset->set('remix.pathes.root_dir', $this->remix_dir);

        $this->preset->remixRequire('versions', 'remix', Preset::APPEND);
        $this->preset->remixRequire('pathes', '', Preset::APPEND);
        foreach ($this->preset->get('remix.pathes') as $key => $value) {
            if ($key === 'root_dir') {
                continue;
            }
            $key = 'remix.pathes.' . $key;
            $this->preset->set($key, $this->remixDir($value));
        }

        return $this;
    }

    public function initializeApp(string $dir): self
    {
        $this->loadPreset();
        $this->app_dir = realpath($dir);

        $env_path = $this->appDir('env.php');
        if (! $env_path) {
            throw new RemixException('app requires env.php');
        }
        $env = require($env_path);
        $env = ($env && $env !== 1) ? $env : 'production';

        $this->preset->appDir($this->appDir('/presets'));

        $env_file = 'env.' . $env;
        $this->preset->require('app', 'app');
        $this->preset->require($env_file, 'app', Preset::APPEND);

        if (Audio::getInstance()->cli) {
            // Do only for CLI
            $this->preset->optional('effector');
        } else {
            // Do only for Web
            $bounce_dir = $this->preset->get('app.pathes.bounce_dir');
            $this->preset->set('app.pathes.bounce_dir', $this->appDir($bounce_dir));

            $tracks_path = $this->appDir('/mixer.php') ?: [];
            Audio::getInstance()->mixer->load($tracks_path);
        }

        return $this;
    }
    // function initializeApp()

    public function loadPreset(): self
    {
        if (! $this->preset) {
            $this->preset = Audio::getInstance()->preset;
        }
        return $this;
    }

    public function preset(): Preset
    {
        return $this->preset;
    }

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
    public function playWeb(): self
    {
        Audio::getInstance()->cli = false;

        $path = $_SERVER['PATH_INFO'] ?? '';
        $studio = Audio::getInstance()->mixer->route($path);

        Delay::log('BODY', $studio->getMimeType());
        $this->reverb = new Reverb($studio, $this->preset);
        return $this;
    }
    // function playWeb()

    public function finalize(): Reverb
    {
        Audio::destroy();
        return $this->reverb;
    }

    public function playCli(array $argv): void
    {
        $audio = Audio::getInstance();
        $audio->amp->initialize($this)->play($argv);
        unset($audio);
        Delay::log('BODY', '');
    }
    // function playCli()
}
// class DAW
