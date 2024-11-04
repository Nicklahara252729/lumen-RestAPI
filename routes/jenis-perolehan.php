<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['middleware' => ['auth:api', 'signature'], 'prefix' => 'jenis-perolehan'], function () use ($router) {
    $router->get('data/{pelayanan}', 'JenisPerolehan\JenisPerolehanController@data');
    $router->get('get/{uuidJenisPerolehan}', 'JenisPerolehan\JenisPerolehanController@get');
    $router->post('store', 'JenisPerolehan\JenisPerolehanController@store');
    $router->put('update/{uuidJenisPerolehan}', 'JenisPerolehan\JenisPerolehanController@update');
    $router->put('update/status/{uuidJenisPerolehan}', 'JenisPerolehan\JenisPerolehanController@updateStatus');
    $router->delete('delete/{uuidJenisPerolehan}', 'JenisPerolehan\JenisPerolehanController@delete');
});
