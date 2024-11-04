<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'akses'], function () use ($router) {
    $router->get('data', 'Akses\AksesController@data');
    $router->get('get/{uuidAkses}', 'Akses\AksesController@get');
    $router->get('get/{role}/{uuidBidang}', 'Akses\AksesController@getByRoleBidang');
    $router->post('store', 'Akses\AksesController@store');
});
