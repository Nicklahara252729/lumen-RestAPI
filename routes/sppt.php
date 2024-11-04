<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'sppt'], function () use ($router) {

    /**
     * get all data
     */
    $router->get('data', 'Sppt\Sppt\SpptController@data');
    $router->get('data/{pageSize}', 'Sppt\Sppt\SpptController@data');

    /**
     * history
     */
    $router->get('history/{kdKecamatan}/{kdKelurahan}/{kdBlok}/{noUrut}/{kdJnsOp}', 'Sppt\Sppt\SpptController@history');

    /**
     * search
     */
    $router->get('search/{kdKecamatan}/{kdKelurahan}/{nama}', 'Sppt\Sppt\SpptController@search');
    $router->get('search/{kdKecamatan}/{kdKelurahan}/{kdBlok}/{noUrut}/{statusKolektif}', 'Sppt\Sppt\SpptController@search');
    $router->get('search/{kdKecamatan}/{kdKelurahan}/{kdBlok}/{noUrut}/{statusKolektif}/{tahun}', 'Sppt\Sppt\SpptController@searchByNopTahun');
    $router->get('search', 'Sppt\Sppt\SpptController@searchByKtp');

    /**
     * tagihan kolektor (petugas lapangan / kelektor)
     */
    $router->get('data-blok', 'Sppt\Sppt\SpptController@dataBlok');
    $router->get('data-nop/{kdBlok}', 'Sppt\Sppt\SpptController@dataNopByBlok');
    $router->get('data-blok-selesai', 'Sppt\Sppt\SpptController@dataBlokSelesai');
    $router->get('data-nop-selesai/{kdBlok}', 'Sppt\Sppt\SpptController@dataNopByBlokSelesai');
    $router->get('get-nop-selesai/{nop}', 'Sppt\Sppt\SpptController@nopSelesai');

    /**
     * tagihan kolektor (superadmin / admin / operator)
     */
    $router->get('data-blok/{kdKecamatan}/{kdKelurahan}', 'Sppt\Sppt\SpptController@dataBlok');
    $router->get('data-nop/{kdBlok}/{kdKecamatan}/{kdKelurahan}', 'Sppt\Sppt\SpptController@dataNopByBlok');
    $router->get('data-blok-selesai/{kdKecamatan}/{kdKelurahan}/{uuidUser}', 'Sppt\Sppt\SpptController@dataBlokSelesai');
    $router->get('data-nop-selesai', 'Sppt\Sppt\SpptController@dataNopByBlokSelesai');

    /**
     * pembayaran manual
     */ 
    $router->group(['prefix' => 'pembayaran-manual'], function () use ($router) {
        $router->get('data', 'Sppt\PembayaranManual\PembayaranManualController@data');
        $router->post('store', 'Sppt\PembayaranManual\PembayaranManualController@store');
    });

    /**
     * pembatalan transaksi
     */
    $router->group(['prefix' => 'pembatalan-transaksi'], function () use ($router) {
        $router->post('store', 'Sppt\PembatalanTransaksi\PembatalanTransaksiController@store');
    });

    /**
     * pembatalan denda
     */
    $router->group(['prefix' => 'pembatalan-denda'], function () use ($router) {
        $router->post('store', 'Sppt\PembatalanDenda\PembatalanDendaController@store');
    });
});
