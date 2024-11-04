<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'dhkp'], function () use ($router) {
    $router->get('data', 'Dhkp\DhkpController@data');    
});
