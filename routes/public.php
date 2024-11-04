<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'public'], function () use ($router) {

    $router->get('slider', 'Public\PublicController@slider');
    $router->get('realisasi', 'Public\PublicController@realisasi');
    $router->get('history/{kdKecamatan}/{kdKelurahan}/{kdBlok}/{noUrut}/{kdJnsOp}', 'Sppt\Sppt\SpptController@history');
});
