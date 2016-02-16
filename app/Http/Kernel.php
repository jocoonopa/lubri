<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'          => \App\Http\Middleware\Authenticate::class,
        'auth.basic'    => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest'         => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'report'        => \App\Http\Middleware\ReportMiddleware::class,
        'honeybaby'     => \App\Http\Middleware\HoneyBabyMiddleware::class,
        'import.task'   => \App\Http\Middleware\ImportTaskMiddleware::class,
        'import.push'   => \App\Http\Middleware\ImportPushMiddleware::class,
        'import.content'=> \App\Http\Middleware\ImportContentMiddleware::class,
        'auth.chinghwa' => \App\Http\Middleware\ChinghwaAuthenticate::class,
        'auth.it'       => \App\Http\Middleware\ITAuthenticate::class,
        'auth.corp'     => \App\Http\Middleware\CorpAuthenticate::class
    ];
}
