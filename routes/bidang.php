<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature']], function () use ($router) {
    /**
     * bidang
     */
    $router->group(['prefix' => 'bidang'], function () use ($router) {
        $router->get('data', 'Bidang\Bidang\BidangController@data');
        $router->get('get/{uuidBidang}', 'Bidang\Bidang\BidangController@get');
        $router->post('store', 'Bidang\Bidang\BidangController@store');
        $router->put('update/{uuidBidang}', 'Bidang\Bidang\BidangController@update');
        $router->delete('delete/{uuidBidang}', 'Bidang\Bidang\BidangController@delete');
    });

    /**
     * sub bidang
     */
    $router->group(['prefix' => 'sub-bidang'], function () use ($router) {
        $router->get('data', 'Bidang\SubBidang\SubBidangController@data');
        $router->get('get/{uuidSubBidang}', 'Bidang\SubBidang\SubBidangController@get');
        $router->get('get/bidang/{param}', 'Bidang\SubBidang\SubBidangController@getByBidang');
        $router->post('store', 'Bidang\SubBidang\SubBidangController@store');
        $router->put('update/{uuidSubBidang}', 'Bidang\SubBidang\SubBidangController@update');
        $router->delete('delete/{uuidSubBidang}', 'Bidang\SubBidang\SubBidangController@delete');
    });
});
