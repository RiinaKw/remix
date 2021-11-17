<?php

use Remix\Track;

// phpcs:disable PSR2.Methods.FunctionCallSignature.SpaceAfterOpenBracket
// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma
return [
    Track::get('/', 'TopChannel@index'),
    Track::get('/vader(/:name)?', 'TopChannel@vader'),
    Track::any('/302', 'TopChannel@redirect302'),
    Track::any('/redirected', 'TopChannel@redirected')->name('redirected'),

    Track::any( '/form/input',   'FormChannel@input')->name('FormInput'),
    Track::post('/form/confirm', 'FormChannel@confirm'),
    Track::post('/form/submit',  'FormChannel@submit'),
];
// phpcs:enable
