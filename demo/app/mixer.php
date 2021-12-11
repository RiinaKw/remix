<?php

use Remix\Track;

// phpcs:disable PSR2.Methods.FunctionCallSignature.SpaceAfterOpenBracket
// phpcs:disable Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma
return [
    Track::get('/', 'TopChannel@index'),
    Track::get('/vader(/:name)?', 'TopChannel@vader'),
    Track::any('/302', 'TopChannel@redirect302'),
    Track::any('/redirected', 'TopChannel@redirected')->name('redirected'),
    Track::get('/closure', function () {
        echo "I'm in closure";
    }),

    // test of exception behavior
    Track::any('/exception/404',     'ExceptionChannel@throw404'),
    Track::any('/exception/405',     'ExceptionChannel@throw405'),
    Track::any('/exception/remix',   'ExceptionChannel@throwRemix'),
    Track::any('/exception/app',     'ExceptionChannel@throwApp'),
    Track::any('/exception/core',    'ExceptionChannel@throwCore'),
    Track::any('/exception/dj',      'ExceptionChannel@throwDJ'),
    Track::any('/exception/error',   'ExceptionChannel@throwError'),
    Track::any('/exception/runtime', 'ExceptionChannel@throwRuntime'),
    Track::any('/exception/native',  'ExceptionChannel@throwNativeError'),
    Track::any('/exception/syntax',  'ExceptionChannel@throwSyntax'),
    Track::any('/exception/trigger/error',   'ExceptionChannel@throwTriggerError'),
    Track::any('/exception/trigger/warning', 'ExceptionChannel@throwTriggerWarning'),
    Track::any('/exception/trigger/notice',  'ExceptionChannel@throwTriggerNotice'),

    // test of form behavior
    Track::get( '/form/input',   'FormChannel@init')->name('FormInput'),
    Track::post('/form/input',   'FormChannel@input'),
    Track::post('/form/confirm', 'FormChannel@confirm'),
    Track::post('/form/submit',  'FormChannel@submit'),
];
// phpcs:enable
