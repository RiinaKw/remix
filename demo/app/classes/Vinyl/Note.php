<?php

namespace App\Vinyl;

class Note extends \Remix\Vinyl
{
    public const TABLE = 'notes';
    public const PK = 'note_id';

    protected static $turntable = \App\Turntable\NoteTurntable::class;
}
// class Note
