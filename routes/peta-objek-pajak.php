<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'peta-objek-pajak'], function () use ($router) {
    $router->get('data/{kdKecamatan}', 'PetaObjekPajak\PetaObjekPajakController@data');
    $router->get('data/{kdKecamatan}/{kdKelurahan}', 'PetaObjekPajak\PetaObjekPajakController@data');
    $router->get('data/{kdKecamatan}/{kdKelurahan}/{blok}', 'PetaObjekPajak\PetaObjekPajakController@data');
});
