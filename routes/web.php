<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    // return $router->app->version();
    $url = request()->getSchemeAndHttpHost();
    $checkHttp = str($url, "http://");
    // $checkHttp = $checkHttp == 'http://' ? "http" : "https";
    return parse_url($url, PHP_URL_SCHEME);
});
