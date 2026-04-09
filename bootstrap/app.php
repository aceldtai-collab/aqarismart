<?php

// Polyfill gethostname() for NativePHP Jump/mobile runtime where it's unavailable
if (!function_exists('gethostname')) {
    function gethostname(): string
    {
        return 'nativephp-mobile';
    }
}

use App\Console\Commands\ExpireResidentListings;
use App\Console\Commands\GenerateTenantDailyReports;
use App\Console\Commands\GenerateTenantAlerts;
use App\Console\Commands\PrelaunchReset;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/tenant.php',
            __DIR__.'/../routes/admin.php',
            __DIR__.'/../routes/landing.php',
            __DIR__.'/../routes/web.php',
        ],
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        GenerateTenantDailyReports::class,
        GenerateTenantAlerts::class,
        PrelaunchReset::class,
        ExpireResidentListings::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust Cloudways reverse proxy (Nginx/Varnish) so scheme, host, and port are detected correctly
        $middleware->trustProxies(at: '*');

        // Alias and register tenant middleware
        $middleware->alias([
            'tenant' => \App\Http\Middleware\IdentifyTenant::class,
            'features' => \App\Http\Middleware\EnsureFeatures::class,
            'superadmin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'role' => \App\Http\Middleware\EnsureTenantRole::class,
            'staff' => \App\Http\Middleware\EnsureStaff::class,
            'resident' => \App\Http\Middleware\EnsureResident::class,
            'mobile.tenant' => \App\Http\Middleware\SetMobileTenantContext::class,
            'setlocale' => \App\Http\Middleware\SetLocale::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);

        // Locale now relies on ?lang + cookie/session, so it can run after cookie encryption.
        if (method_exists($middleware, 'appendToGroup')) {
            $middleware->appendToGroup('web', [\App\Http\Middleware\SetLocale::class]);
        } else {
            $middleware->appendToGroup('web', [\App\Http\Middleware\SetLocale::class]);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders([
        \App\Providers\TenancyServiceProvider::class,
        \App\Providers\AuthServiceProvider::class,
    ])
    ->create();
