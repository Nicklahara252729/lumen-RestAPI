<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'region'], function () use ($router) {
    /**
     * provinsi
     */
    $router->group(['prefix' => 'provinsi'], function () use ($router) {
        $router->get('data', 'Region\Provinsi\ProvinsiController@data');
        $router->get('get/{param}', 'Region\Provinsi\ProvinsiController@get');
    });

    /**
     * kabupaten
     */
    $router->group(['prefix' => 'kabupaten'], function () use ($router) {
        $router->get('data/{idProvinsi}', 'Region\Kabupaten\KabupatenController@data');
        $router->get('get/{param}', 'Region\Kabupaten\KabupatenController@get');
    });

    /**
     * kecamatan
     */
    $router->group(['prefix' => 'kecamatan'], function () use ($router) {
        $router->get('data/{idKabupaten}', 'Region\Kecamatan\KecamatanController@data');
        $router->get('get/{param}', 'Region\Kecamatan\KecamatanController@get');
    });

    /**
     * kelurahan
     */
    $router->group(['prefix' => 'kelurahan'], function () use ($router) {
        $router->get('data/{idKecamatan}', 'Region\Kelurahan\KelurahanController@data');
        $router->get('get/{param}', 'Region\Kelurahan\KelurahanController@get');
    });
});
