<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'kaban'], function () use ($router) {
    $router->get('sts', 'Kaban\KabanController@sts');
    $router->get('sts-tertinggi', 'Kaban\KabanController@stsTertinggi');
    $router->get('sts-detail/{noSts}', 'Kaban\KabanController@detailSts');
});
