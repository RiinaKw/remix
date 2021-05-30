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

    Track::any('/sample', 'SampleChannel@index'),
    Track::any('/sample/xml', 'SampleChannel@xml'),
    Track::any('/sample/json', 'SampleChannel@json'),
    Track::any('/sample/error(/:code)?', 'SampleChannel@error'),
    Track::any('/sample/exception(/:code)?', 'SampleChannel@exception'),
];
