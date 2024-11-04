<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature']], function () use ($router) {
    /**
     * layanan
     */
    $router->group(['prefix' => 'layanan'], function () use ($router) {
        $router->get('data', 'Layanan\Layanan\LayananController@data');
        $router->get('get/{uuidLayanan}', 'Layanan\Layanan\LayananController@get');
        $router->post('store', 'Layanan\Layanan\LayananController@store');
        $router->put('update/{uuidLayanan}', 'Layanan\Layanan\LayananController@update');
        $router->delete('delete/{uuidLayanan}', 'Layanan\Layanan\LayananController@delete');
    });

    /**
     * jenis layanan
     */
    $router->group(['prefix' => 'jenis-layanan'], function () use ($router) {
        $router->get('data/{status}', 'Layanan\JenisLayanan\JenisLayananController@data');
        $router->get('get/{uuidJenisLayanan}', 'Layanan\JenisLayanan\JenisLayananController@get');
        $router->post('store', 'Layanan\JenisLayanan\JenisLayananController@store');
        $router->put('update/{uuidJenisLayanan}', 'Layanan\JenisLayanan\JenisLayananController@update');
        $router->delete('delete/{uuidJenisLayanan}', 'Layanan\JenisLayanan\JenisLayananController@delete');
    });
});
