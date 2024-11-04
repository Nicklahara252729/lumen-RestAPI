<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'pelayanan'], function () use ($router) {
    $router->get('generate-nomor', 'Pelayanan\Pelayanan\PelayananController@generate');
    $router->get('data/{param}', 'Pelayanan\Pelayanan\PelayananController@data');
    $router->get('data/{param}/{pageSize}', 'Pelayanan\Pelayanan\PelayananController@data');
    $router->get('data/realisasi/kecamatan/{kdKecamatan}/{tahun}', 'Pelayanan\Pelayanan\PelayananController@dataRealisasiKecamatan');
    $router->get('data/piutang/kecamatan/{kdKecamatan}/{tahun}', 'Pelayanan\Pelayanan\PelayananController@dataPiutangKecamatan');
    $router->get('data/wp/kecamatan/{kdKecamatan}/{tahun}', 'Pelayanan\Pelayanan\PelayananController@dataJumlahWpKecamatan');
    $router->get('data/realisasi/kelurahan/{kdKecamatan}/{kdKelurahan}/{tahun}', 'Pelayanan\Pelayanan\PelayananController@dataRealisasiKelurahan');
    $router->get('data/piutang/kelurahan/{kdKecamatan}/{kdKelurahan}/{tahun}', 'Pelayanan\Pelayanan\PelayananController@dataPiutangKelurahan');
    $router->get('data/wp/kelurahan/{kdKecamatan}/{kdKelurahan}/{tahun}', 'Pelayanan\Pelayanan\PelayananController@dataJumlahWpKelurahan');
    $router->get('search/{param}/{pageSize}', 'Pelayanan\Pelayanan\PelayananController@search');
    $router->get('count-sppt/{kdKecamatan}/{kdKelurahan}/{kdBlok}/{noUrutAwal}/{noUrutAkhir}/{statusKolektif}', 'Pelayanan\Pelayanan\PelayananController@countSppt');
    $router->get('autocomplete/{pelayanan}/{nop}', 'Pelayanan\Pelayanan\PelayananController@autocomplete');
    $router->get('autocomplete/{pelayanan}/{nop}/{tahun}', 'Pelayanan\Pelayanan\PelayananController@autocomplete');
    $router->get('count-nop', 'Pelayanan\Pelayanan\PelayananController@countNop');
});