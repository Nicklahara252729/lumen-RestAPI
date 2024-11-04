<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'pengutip'], function () use ($router) {
    $router->get('data', 'Pengutip\PengutipController@data');
    $router->get('data/restoran', 'Pengutip\PengutipController@dataRestoran');
    $router->get('autocomplete/{nopd}', 'Pengutip\PengutipController@autocomplete');
    $router->post('store', 'Pengutip\PengutipController@store');
});
