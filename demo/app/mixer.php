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
    Track::any('/sample/text', 'SampleChannel@text')->name('text'),
    Track::any('/sample/xml', 'SampleChannel@xml')->name('xml'),
    Track::any('/sample/json', 'SampleChannel@json')->name('json'),
    Track::any('/sample/status(/:code)?', 'SampleChannel@status')->name('status'),
    Track::any('/sample/exception(/:code)?', 'SampleChannel@exception')->name('exception'),
    Track::any('/sample/api', 'SampleChannel@api')->name('api')->api(),

    /**** scaffold for "post" ****/
    Track::get(   '/post',                'PostChannel@list'    )->name('post.list'),
    //Track::get(   '/post/new',            'PostChannel@new'     )->name('post.new'),
    Track::put(   '/post/new',            'PostChannel@doInsert')->name('post.new'),

    Track::post(  '/post/confirm(/:id)?', 'PostChannel@confirm' )->name('post.confirm'),

    Track::get(   '/post/:id',            'PostChannel@show'    )->name('post.show'),
    //Track::get(   '/post/(?<id>\d+)/edit',       'PostChannel@edit'    )->name('post.edit'),
    Track::put(   '/post/:id/edit',       'PostChannel@doUpdate')->name('post.edit'),






    //Track::get(   '/post/:id/delete',     'PostChannel@delete'  )->name('post.delete'),
    Track::delete('/post/:id/delete',     'PostChannel@doDelete')->name('post.delete'),
];
