<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'Auth\Login\LoginController@login');
    $router->post('logout', 'Auth\Logout\LogoutController@logout');
    $router->post('register/notaris', 'Auth\Register\RegisterController@storeNotaris');
});
