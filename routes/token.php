<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'token'], function () use ($router) {
    $router->post('refresh', 'Token\TokenController@refresh');
    $router->post('validation', 'Token\TokenController@validation');
});