<?php

namespace App\Vinyl;

class Note extends \Remix\Vinyl
{
    public static $table = 'notes';
    public static $pk = 'note_id';

    protected static $turntable = \App\Turntable\NoteTurntable::class;
}
// class Note
