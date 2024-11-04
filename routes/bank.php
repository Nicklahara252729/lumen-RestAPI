<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/**
 * briva
 */
$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'briva'], function () use ($router) {
    $router->post('create', 'Bank\Briva\BrivaController@create');
});

/**
 * BPN
 */
$router->put('bphtb-service/{uuidPelayananBphtb}', 'Bank\Bpn\BpnController@bphtbService');
$router->post('getBPHTBService', 'Bank\Bpn\BpnController@getBPHTBService');
$router->post('getPBBService', 'Bank\Bpn\BpnController@getPBBService');