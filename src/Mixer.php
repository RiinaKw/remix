<?php

namespace Remix;

/**
 * Remix Mixer : routing
 */
class Mixer extends Component
{

    protected $tracks = [];

    public function __construct()
    {
        parent::__construct();

        $remix = \Remix\App::getInstance();
        $tracks_path = $remix->appDir('/mixer.php');
        $this->tracks = require($tracks_path);
    } // function __construct()

    public function route(string $path)
    {
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        foreach ($this->tracks as $track) {
            if ($track->match($path)) {
                return $track->call();
            }
        }
        throw new \Remix\Exceptions\HttpException('it did not match any route, given ' . $path, 404);
    } // function route()
} // class Mixer
