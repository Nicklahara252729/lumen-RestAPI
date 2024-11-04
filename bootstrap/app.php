<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();
$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');
$app->configure('database');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    App\Http\Middleware\CorsMiddleware::class
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'signature' => App\Http\Middleware\Signature::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\RepositoriesServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
$app->register(Maatwebsite\Excel\ExcelServiceProvider::class);

/**
 * set language
 */
app('translator')->setLocale('id');

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

$app->router->group([
    'prefix' => 'api',
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/auth.php';
    require __DIR__ . '/../routes/token.php';
    require __DIR__ . '/../routes/user.php';
    require __DIR__ . '/../routes/bidang.php';
    require __DIR__ . '/../routes/setting.php';
    require __DIR__ . '/../routes/layanan.php';
    require __DIR__ . '/../routes/pelayanan.php';
    require __DIR__ . '/../routes/pelayanan-pbb.php';
    require __DIR__ . '/../routes/pelayanan-bphtb.php';
    require __DIR__ . '/../routes/region.php';
    require __DIR__ . '/../routes/refrensi.php';    
    require __DIR__ . '/../routes/akses.php';
    require __DIR__ . '/../routes/dashboard.php';
    require __DIR__ . '/../routes/print.php';
    require __DIR__ . '/../routes/pbb-minimal.php';
    require __DIR__ . '/../routes/sppt.php';
    require __DIR__ . '/../routes/report.php';
    require __DIR__ . '/../routes/dhkp.php';
    require __DIR__ . '/../routes/public.php';
    require __DIR__ . '/../routes/jenis-perolehan.php';
    require __DIR__ . '/../routes/master-data.php';
    require __DIR__ . '/../routes/notaris.php';
    require __DIR__ . '/../routes/tunggakan.php';
    require __DIR__ . '/../routes/tagihan-kolektor.php';
    require __DIR__ . '/../routes/skpdkb.php';
    require __DIR__ . '/../routes/bank.php';
    require __DIR__ . '/../routes/log.php';
    require __DIR__ . '/../routes/peta-objek-pajak.php';
    require __DIR__ . '/../routes/pat.php';
    require __DIR__ . '/../routes/reklame.php';
    require __DIR__ . '/../routes/kaban.php';
    require __DIR__ . '/../routes/operator-lapangan.php';
    require __DIR__ . '/../routes/pengutip.php';
});

return $app;
