<?php

use Remix\Track;

return [
    Track::get('/', 'TopChannel@index')->name('top'),
    Track::get('/bounce(/:message)?', 'TopChannel@bounce'),

    Track::get('/cb', function () {
        return '<b>from callback</b>';
    }),
    Track::get('/json', 'TopChannel@json'),
    Track::get('/redirect', 'TopChannel@redirect'),
    Track::get('/exception', 'TopChannel@exception'),

    [
        Track::get('/api/:id', 'ApiChannel@test')->api(),
    ],

    Track::get('/form/:id', 'FormChannel@index')->name('form'),
    Track::post('/form/:id', 'FormChannel@post'),
    Track::post('/postonly', function () {
        return 'this page is post only';
    }),
];
