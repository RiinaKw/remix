<?php

namespace App\Turntable;

use Remix\Turntable;

class NoteTurntable extends Turntable
{
    public function htmlId(): string
    {
        return "<strong>{$this->name}</strong>";
    }

    public function urlApi(): string
    {
        return "api/{$this->note_id}.json";
    }
}
// class NoteTurntable
