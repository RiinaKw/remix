<?php

use \Remix\Track;

return [
    Track::get('/', 'TopChannel@index')->name('top'),
    Track::get('/bounce', 'TopChannel@bounce'),
    Track::get('/bounce/:message', 'TopChannel@bounce'),
    Track::get('/cb', function () {
        return '<b>from callback</b>';
    }),
    Track::get('/json', 'TopChannel@json'),
    Track::get('/redirect', 'TopChannel@redirect'),
    Track::get('/exception', 'TopChannel@exception'),
];
