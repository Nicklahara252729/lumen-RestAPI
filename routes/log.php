<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'log'], function () use ($router) {
    /**
     * bphtb
     */
    $router->group(['prefix' => 'bphtb'], function () use ($router) {
        $router->get('data/{noRegistrasi}', 'Log\Bphtb\LogBphtbController@data');
    });
});
