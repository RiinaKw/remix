<?php

use Remix\Track;

return [
    Track::get('/', 'TopChannel@index'),
    Track::get('/vader(/:name)?', 'TopChannel@vader'),
];
