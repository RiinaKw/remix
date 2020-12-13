<?php

namespace App\Turntable;

use Remix\Turntable;

class NoteTurntable extends Turntable
{
    public function htmlId(): string
    {
        return sprintf('<strong>%s</strong>', $this->name);
    }

    public function urlApi(): string
    {
        return sprintf('api/%d.json', $this->note_id);
    }
}
// class NoteTurntable
