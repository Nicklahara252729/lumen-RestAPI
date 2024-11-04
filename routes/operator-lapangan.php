<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'operator-lapangan'], function () use ($router) {
    $router->get('data', 'OperatorLapangan\OperatorLapanganController@data');
    $router->get('data/hiburan', 'OperatorLapangan\OperatorLapanganController@dataHiburan');
    $router->get('data/hotel', 'OperatorLapangan\OperatorLapanganController@dataHotel');
    $router->get('data/parkir', 'OperatorLapangan\OperatorLapanganController@dataParkir');
    $router->get('data/pat', 'OperatorLapangan\OperatorLapanganController@dataPat');
    $router->get('data/penerangan', 'OperatorLapangan\OperatorLapanganController@dataPenerangan');
    $router->get('data/pln', 'OperatorLapangan\OperatorLapanganController@dataPln');
    $router->get('data/reklame', 'OperatorLapangan\OperatorLapanganController@dataReklame');
    $router->get('data/walet', 'OperatorLapangan\OperatorLapanganController@dataWalet');
    $router->get('autocomplete', 'OperatorLapangan\OperatorLapanganController@autocomplete');
    $router->post('store', 'OperatorLapangan\OperatorLapanganController@store');
    $router->post('store/regpribadi', 'OperatorLapangan\OperatorLapanganController@storeRegpribadi');
    $router->post('store/nopd', 'OperatorLapangan\OperatorLapanganController@storeNopd');
    $router->get('data/regpribadi', 'OperatorLapangan\OperatorLapanganController@dataRegpribadi');
    $router->get('data/nopd', 'OperatorLapangan\OperatorLapanganController@dataNopd');
    $router->get('search', 'OperatorLapangan\OperatorLapanganController@search');
});
