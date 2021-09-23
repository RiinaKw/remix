<?php

use Remix\Track;

// phpcs:disable PSR2.Methods.FunctionCallSignature.SpaceAfterOpenBracket
// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma
return [
    Track::get('/', 'TopChannel@index'),
    Track::get('/vader(/:name)?', 'TopChannel@vader'),

    Track::any( '/form/input',   'FormChannel@input'),
    Track::post('/form/confirm', 'FormChannel@confirm'),
    Track::post('/form/submit',  'FormChannel@submit'),
];
// phpcs:enable
