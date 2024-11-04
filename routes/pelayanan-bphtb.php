<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'pelayanan'], function () use ($router) {

    /**
     * bphtb
     */
    $router->group(['prefix' => 'bphtb'], function () use ($router) {
        $router->post('store', 'Pelayanan\Bphtb\BphtbController@store');
        $router->get('data', 'Pelayanan\Bphtb\BphtbController@data');
        $router->get('data/{pageSize}', 'Pelayanan\Bphtb\BphtbController@data');
        $router->get('autocomplete/{nop}', 'Pelayanan\Bphtb\BphtbController@autocomplete');
        $router->get('autocomplete/{nop}/{tahun}', 'Pelayanan\Bphtb\BphtbController@autocomplete');
        $router->get('get/{param}', 'Pelayanan\Bphtb\BphtbController@get');
        $router->put('update/{uuidPelayananBphtb}', 'Pelayanan\Bphtb\BphtbController@update');
        $router->put('update/status-verifikasi/{uuidPelayananBphtb}', 'Pelayanan\Bphtb\BphtbController@updateStatusVerifikasi');
        $router->get('history-ditolak/{noRegistrasi}', 'Pelayanan\Bphtb\BphtbController@riwayatDitolak');
        $router->get('detail/{uuidPelayananBphtb}', 'Pelayanan\Bphtb\BphtbController@detail');
        $router->post('store/status-ditolak', 'Pelayanan\Bphtb\BphtbController@storeStatusDitolak');
        $router->get('search', 'Pelayanan\Bphtb\BphtbController@search');
        $router->put('update/perhitungan-njop/{uuidPelayananBphtb}', 'Pelayanan\Bphtb\BphtbController@updatePerhitunganNjop');
        $router->put('update/perhitungan-bphtb/{uuidPelayananBphtb}', 'Pelayanan\Bphtb\BphtbController@updatePerhitunganBphtb');
        $router->delete('delete/dokumen/{uuidPelayananBphtb}/{dokumen}', 'Pelayanan\Bphtb\BphtbController@deleteDokumen');
        $router->delete('delete/{uuidPelayananBphtb}', 'Pelayanan\Bphtb\BphtbController@delete');
        $router->post('store/pembayaran-manual', 'Pelayanan\Bphtb\BphtbController@storePembayaranManual');
        $router->put('update-full/{uuidPelayananBphtb}', 'Pelayanan\Bphtb\BphtbController@updateFull');
    });
});
