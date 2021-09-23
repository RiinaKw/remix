<?php

use Remix\Track;

return [
    Track::get('/', 'TopChannel@index'),
    Track::get('/vader(/:name)?', 'TopChannel@vader'),

    Track::get('/form', 'FormChannel@index'),
    Track::post('/form', 'FormChannel@submit'),
];
