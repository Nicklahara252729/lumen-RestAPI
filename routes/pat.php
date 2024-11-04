<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'pat'], function () use ($router) {
    $router->get('data/{status}', 'Pat\PatController@data');
    $router->get('data-sts', 'Pat\PatController@dataSts');
    $router->get('sts-tertinggi', 'Pat\PatController@stsTertinggi');
    $router->put('verifikasi/{idPat}', 'Pat\PatController@verifikasi');
});
