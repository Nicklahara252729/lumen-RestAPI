<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $url = request()->getSchemeAndHttpHost();
        $checkHttp = $url != "" ?  parse_url($url, PHP_URL_SCHEME) : "https";
        Schema::defaultStringLength(191);
        URL::forceScheme($checkHttp);
    }
}
