<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'dashboard'], function () use ($router) {
    $router->get('total-permohonan', 'Dashboard\DashboardController@totalPermohonan');
    $router->get('total-per-layanan', 'Dashboard\DashboardController@totalPerLayanan');
    $router->get('total-by-kecamatan/{kdKecamatan}', 'Dashboard\DashboardController@dataByKecamatanOrKelurahan');
    $router->get('total-by-kelurahan/{kdKecamatan}/{kdKelurahan}', 'Dashboard\DashboardController@dataByKecamatanOrKelurahan');
    $router->get('total-permohonan-bphtb', 'Dashboard\DashboardController@totalPermohonanBphtb');
});
