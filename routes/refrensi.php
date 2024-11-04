<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'refrensi'], function () use ($router) {
    /**
     * pekerjaan
     */
    $router->group(['prefix' => 'pekerjaan'], function () use ($router) {
        $router->get('data', 'Refrensi\Pekerjaan\PekerjaanController@data');
        $router->get('get/{param}', 'Refrensi\Pekerjaan\PekerjaanController@get');
    });

    /**
     * provinsi
     */
    $router->group(['prefix' => 'provinsi'], function () use ($router) {
        $router->get('data', 'Refrensi\Provinsi\ProvinsiController@data');
    });

    /**
     * kecamatan
     */
    $router->group(['prefix' => 'kecamatan'], function () use ($router) {
        $router->get('data', 'Refrensi\Kecamatan\KecamatanController@data');
        $router->get('pad/data', 'Refrensi\Kecamatan\KecamatanController@dataPAD');
    });

    /**
     * kelurahan
     */
    $router->group(['prefix' => 'kelurahan'], function () use ($router) {
        $router->get('data', 'Refrensi\Kelurahan\KelurahanController@getAll');
        $router->get('data/{kdKecamatan}', 'Refrensi\Kelurahan\KelurahanController@data');
        $router->get('pad/data/{kecamatanId}', 'Refrensi\Kelurahan\KelurahanController@dataPAD');
    });

    /**
     * blok
     */
    $router->group(['prefix' => 'blok'], function () use ($router) {
        $router->get('data', 'Refrensi\Blok\BlokController@getAll');
        $router->get('data/{kdKecamatan}/{kdKelurahan}', 'Refrensi\Blok\BlokController@data');
    });

    /**
     * kelas bumi
     */
    $router->group(['prefix' => 'kelas-bumi'], function () use ($router) {
        $router->get('data', 'Refrensi\KelasBumi\KelasBumiController@data');
    });

    /**
     * LSPOP
     */
    $router->group(['prefix' => 'lspop'], function () use ($router) {
        $router->get('jpb/data', 'Refrensi\Lspop\Jpb\JpbController@data');
        $router->get('pekerjaan/data/{namaPekerjaan}', 'Refrensi\Lspop\Pekerjaan\PekerjaanController@data');
    });

    /**
     * jenis pajak
     */
    $router->group(['prefix' => 'jenis-pajak'], function () use ($router) {
        $router->get('pad/data', 'Refrensi\JenisPajak\JenisPajakController@data');
    });
});
