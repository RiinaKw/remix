<?php

namespace Remix;

/**
 * Remix Mixer : routing
 */
class Mixer extends Component
{

    protected $tracks = [];
    protected $named = [];

    public function __construct()
    {
        parent::__construct();

        $remix = App::getInstance();
        $tracks_path = $remix->appDir('/mixer.php');

        //$this->load(require($tracks_path));
    } // function __construct()

    public function load(array $tracks)
    {
        $this->tracks = $tracks;

        foreach ($this->tracks as $track) {
            $name = $track->name;
            if ($name) {
                $this->named[$name] = $track;
            }
        }
        return $this;
    }

    public function route(string $path) : Studio
    {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        foreach ($this->tracks as $track) {
            if ($track->match($path)) {
                $sampler = $track->sampler($path);
                return $track->call($sampler);
            }
        }
        throw new Exceptions\HttpException('it did not match any route, given ' . $path, 404);
    } // function route()

    public function named($named)
    {
        return $this->named[$named] ?? null;
    }
} // class Mixer
