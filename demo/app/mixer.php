<?php

use Remix\Track;

return [
    Track::get('/', function () {
        return '<b>I am your Remix.</b>';
    }),
];
