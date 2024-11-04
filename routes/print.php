<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'print'], function () use ($router) {
    $router->get('permohonan/{noPelayanan}', 'Print\PrintController@permohonan');
    $router->get('surat-keterangan-njop/{param}', 'Print\PrintController@suratKeteranganNjop');
    $router->post('sppt', 'Print\PrintController@sppt');
    $router->get('sppt/{kdKecamatan}/{kdKelurahan}/{kdBlok}/{tahun}', 'Print\PrintController@spptMasal');
    $router->get('sppt/buku45/{kdKecamatan}/{kdKelurahan}/{kdBlok}/{tahun}', 'Print\PrintController@spptBuku45');
    $router->get('stts/{kdKecamatan}/{kdKelurahan}/{kdBlok}/{noUrut}/{statusKolektif}/{tahun}', 'Print\PrintController@stts');
    $router->post('sppt/masal-multiple', 'Print\PrintController@spptMasalMultiple');
    $router->get('sppd-bphtb/{uuidPelayananBphtb}', 'Print\PrintController@sspd');
    $router->get('skpdkb/{sspd}', 'Print\PrintController@skpdkb');
});
