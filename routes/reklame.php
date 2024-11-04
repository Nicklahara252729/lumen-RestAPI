<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'reklame'], function () use ($router) {
    $router->get('data/{status}', 'Reklame\ReklameController@data');
    $router->get('data-sts', 'Reklame\ReklameController@dataSts');
    $router->get('sts-tertinggi', 'Reklame\ReklameController@stsTertinggi');
    $router->put('verifikasi/{idReklame}', 'Reklame\ReklameController@verifikasi');
});
