<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature']], function () use ($router) {
    
    /**
     * slider
     */
    $router->group(['prefix' => 'slider'], function () use ($router) {
        $router->get('data', 'Setting\Slider\SliderController@data');
        $router->get('get/{uuidSlider}', 'Setting\Slider\SliderController@get');
        $router->post('store', 'Setting\Slider\SliderController@store');
        $router->put('update/{uuidSlider}', 'Setting\Slider\SliderController@update');
        $router->delete('delete/{uuidSlider}', 'Setting\Slider\SliderController@delete');
    });

    /**
     * general setting
     */
    $router->group(['prefix' => 'setting'], function () use ($router) {
        $router->get('data', 'Setting\General\SettingController@data');
        $router->get('get/{param}', 'Setting\General\SettingController@get');
        $router->put('update/{uuidSetting}', 'Setting\General\SettingController@update');
    });

    /**
     * layanan setting
     */
    $router->put('layanan/update-status/{uuidLayanan}', 'Setting\Layanan\LayananController@updateStatus');

    /**
     * menu setting
     */
    $router->group(['prefix' => 'menu'], function () use ($router) {
        $router->get('data', 'Setting\Menu\MenuController@data');
        $router->get('get/{uuidMenu}', 'Setting\Menu\MenuController@get');
        $router->post('store', 'Setting\Menu\MenuController@store');
        $router->put('update/{uuidMenu}', 'Setting\Menu\MenuController@update');
        $router->delete('delete/{uuidMenu}', 'Setting\Menu\MenuController@delete');
    });
});
