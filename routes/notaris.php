<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'notaris'], function () use ($router) {
    $router->get('data', 'Notaris\NotarisController@data');
    $router->get('get/{uuidUser}', 'Notaris\NotarisController@get');
    $router->put('verifikasi/{uuidUser}', 'Notaris\NotarisController@verifikasi');
    $router->put('update/{uuidUser}', 'Notaris\NotarisController@update');
    $router->delete('delete/{uuidUser}', 'Notaris\NotarisController@delete');
    $router->post('store', 'Notaris\NotarisController@store');
    $router->get('search', 'Notaris\NotarisController@search');
});
