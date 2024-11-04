<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature']], function () use ($router) {
    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->get('data', 'User\UserController@data');
        $router->get('get/{param}', 'User\UserController@get');
        $router->post('store', 'User\UserController@store');
        $router->put('update/{uuidUser}', 'User\UserController@update');
        $router->put('update/password/{uuidUser}', 'User\UserController@updatePassword');
        $router->delete('delete/{uuidUser}', 'User\UserController@delete');
        $router->get('search', 'User\UserController@search');
    });
});
