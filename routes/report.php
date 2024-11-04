<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/**
 * PBB report
 */
$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'report/pbb'], function () use ($router) {

    /**
     * ketetapan
     */
    $router->group(['prefix' => 'ketetapan'], function () use ($router) {
        $router->get('detail/{tahun}', 'Report\Pbb\ReportPbbController@detailKetetapan');
        $router->get('rekap/{tahun}', 'Report\Pbb\ReportPbbController@rekapKetetapan');
        $router->get('rincian', 'Report\Pbb\ReportPbbController@rincianKetetapan');
    });

    /**
     * realisasi
     */
    $router->group(['prefix' => 'realisasi'], function () use ($router) {
        $router->get('rincian', 'Report\Pbb\ReportPbbController@rincianRealisasi');
        $router->get('rekap/{startDate}/{endDate}', 'Report\Pbb\ReportPbbController@rekapRealisasi');
        $router->get('detail/{startDate}/{endDate}', 'Report\Pbb\ReportPbbController@detailRealisasi');
    });

    /**
     * piutang
     */
    $router->group(['prefix' => 'piutang'], function () use ($router) {
        $router->get('rincian', 'Report\Pbb\ReportPbbController@rincianPiutang');
        $router->get('rekap', 'Report\Pbb\ReportPbbController@rekapPiutang');
    });
});

/**
 * BPHTB report
 */
$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'report'], function () use ($router) {

    /**
     * notaris
     */
    $router->group(['prefix' => 'notaris'], function () use ($router) {
        $router->post('data', 'Report\Bphtb\Notaris\ReportNotarisController@data');
    });

    /**
     * skpdkb
     */
    $router->group(['prefix' => 'skpdkb'], function () use ($router) {
        $router->get('data/{statusBayar}', 'Report\Bphtb\Skpdkb\ReportSkpdkbController@data');
    });
});
