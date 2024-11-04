<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'skpdkb'], function () use ($router) {
    $router->get('data', 'Skpdkb\SkpdkbController@data');
    $router->post('store', 'Skpdkb\SkpdkbController@store');
});
