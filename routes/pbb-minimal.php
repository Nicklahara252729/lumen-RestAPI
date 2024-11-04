<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'pbb-minimal'], function () use ($router) {
    $router->get('data', 'PbbMinimal\PbbMinimalController@data');
    $router->get('get/{thnPbbMinimal}', 'PbbMinimal\PbbMinimalController@get');
    $router->post('store', 'PbbMinimal\PbbMinimalController@store');
    $router->put('update/{thnPbbMinimal}', 'PbbMinimal\PbbMinimalController@update');
    $router->delete('delete/{thnPbbMinimal}', 'PbbMinimal\PbbMinimalController@delete');
});
