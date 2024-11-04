<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'tagihan-kolektor'], function () use ($router) {

    /**
     * petugas lapangan
     */
    $router->get('data', 'TagihanKolektor\TagihanKolektorController@data');
    $router->get('data/{pageSize}', 'TagihanKolektor\TagihanKolektorController@data');
    $router->get('get/{nop}', 'TagihanKolektor\TagihanKolektorController@get');
    $router->post('store', 'TagihanKolektor\TagihanKolektorController@store');
});
