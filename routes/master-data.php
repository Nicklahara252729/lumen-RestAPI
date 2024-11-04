<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'master-data'], function () use ($router) {

    /**
     * npoptkp
     */
    $router->group(['prefix' => 'npoptkp'], function () use ($router) {
        $router->get('data', 'MasterData\Npoptkp\NpoptkpController@data');
        $router->get('get/{uuidNpoptkp}', 'MasterData\Npoptkp\NpoptkpController@get');
        $router->post('store', 'MasterData\Npoptkp\NpoptkpController@store');
        $router->put('update/{uuidNpoptkp}', 'MasterData\Npoptkp\NpoptkpController@update');
        $router->delete('delete/{uuidNpoptkp}', 'MasterData\Npoptkp\NpoptkpController@delete');
    });

});
