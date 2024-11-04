<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'tunggakan'], function () use ($router) {

    /**
     * petugas lapangan
     */
    $router->get('data', 'Tunggakan\TunggakanController@data');
    $router->get('data/{kdBlok}', 'Tunggakan\TunggakanController@dataNopByKdBlok');
    $router->put('update/{param}', 'Tunggakan\TunggakanController@update');

    /**
     * superadmin / admin / operator
     */
    $router->get('data/{kdKecamatan}/{kdKelurahan}', 'Tunggakan\TunggakanController@data');
    $router->get('data/{kdBlok}/{kdKecamatan}/{kdKelurahan}', 'Tunggakan\TunggakanController@dataNopByKdBlok');
});
