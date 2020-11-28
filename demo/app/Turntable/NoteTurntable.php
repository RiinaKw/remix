<?php

namespace App\Turntable;

use \Remix\Turntable;

class NoteTurntable extends Turntable
{
    public function htmlId()
    {
        return sprintf('<strong>%s</strong>', $this->name);
    }

    public function urlApi()
    {
        return sprintf('api/%d.json', $this->note_id);
    }
} // class NoteTurntable
