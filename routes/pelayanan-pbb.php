<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'pelayanan'], function () use ($router) {
    /**
     * 
     * pendaftaran data baru
     */
    $router->group(['prefix' => 'pendaftaran'], function () use ($router) {
        $router->post('store', 'Pelayanan\Pbb\Pendaftaran\PendaftaranController@store');
        $router->post('store-lspop', 'Pelayanan\Pbb\Pendaftaran\PendaftaranController@storeLspop');
        $router->get('get/{param}', 'Pelayanan\Pbb\Pendaftaran\PendaftaranController@get');
        $router->put('update/{uuidPelayanan}', 'Pelayanan\Pbb\Pendaftaran\PendaftaranController@update');
        $router->put('update/status-verifikasi/{uuidPelayanan}', 'Pelayanan\Pbb\Pendaftaran\PendaftaranController@updateStatusVerifikasi');
        $router->delete('delete/{uuidPelayanan}/{uuidUser}', 'Pelayanan\Pbb\Pendaftaran\PendaftaranController@delete');
        $router->get('export', 'Pelayanan\Pbb\Pendaftaran\PendaftaranController@export');
    });

    /**
     * pecah NOP
     */
    $router->group(['prefix' => 'pecah-nop'], function () use ($router) {
        $router->post('store', 'Pelayanan\Pbb\PecahNop\PecahNopController@store');
        $router->put('update/status-verifikasi/{uuidPelayanan}', 'Pelayanan\Pbb\PecahNop\PecahNopController@updateStatusVerifikasi');
        $router->get('data', 'Pelayanan\Pbb\PecahNop\PecahNopController@data');
        $router->get('data/{pageSize}', 'Pelayanan\Pbb\PecahNop\PecahNopController@data');
    });

    /**
     * gabung NOP
     */
    $router->group(['prefix' => 'gabung-nop'], function () use ($router) {
        $router->post('store', 'Pelayanan\Pbb\GabungNop\GabungNopController@store');
    });

    /**
     * perubahan status NOP
     */
    $router->group(['prefix' => 'perubahan-status-nop'], function () use ($router) {
        $router->get('data', 'Pelayanan\Pbb\PerubahanStatusNop\PerubahanStatusNopController@data');
        $router->get('data/{pageSize}', 'Pelayanan\Pbb\PerubahanStatusNop\PerubahanStatusNopController@data');
        $router->put('update', 'Pelayanan\Pbb\PerubahanStatusNop\PerubahanStatusNopController@update');
    });

    /**
     * peta objek pajak
     */
    $router->group(['prefix' => 'peta-op'], function () use ($router) {
        $router->post('store', 'Pelayanan\Pbb\PetaObjekPajak\PetaObjekPajakController@store');
        $router->get('data', 'Pelayanan\Pbb\PetaObjekPajak\PetaObjekPajakController@data');
    });

    /**
     * mutasi
     */
    $router->group(['prefix' => 'mutasi'], function () use ($router) {
        $router->post('store', 'Pelayanan\Pbb\Mutasi\MutasiController@store');
    });

    /**
     * pembatalan sppt
     */
    $router->group(['prefix' => 'pembatalan-sppt'], function () use ($router) {
        $router->post('store', 'Pelayanan\Pbb\PembatalanSppt\PembatalanSpptController@store');
    });

    /**
     * penetapan sppt
     */
    $router->group(['prefix' => 'penetapan-sppt'], function () use ($router) {
        $router->post('store', 'Pelayanan\Pbb\PenetapanSppt\PenetapanSpptController@store');
    });

    /**
     * LSPOP
     */
    $router->group(['prefix' => 'lspop'], function () use ($router) {
        $router->post('store', 'Pelayanan\Pbb\Lspop\LspopController@store');
    });
});
