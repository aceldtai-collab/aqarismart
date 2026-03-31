<?php

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileDashboardController;
use App\Http\Controllers\Api\MobileMarketplaceController;
use App\Http\Controllers\Api\MobileTenantDirectoryController;
use App\Http\Controllers\Api\MobileUnitController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->name('api.mobile.')->group(function () {
    Route::post('/auth/register-business', [MobileAuthController::class, 'registerBusiness'])->name('auth.register-business');
    Route::post('/auth/register-resident', [MobileAuthController::class, 'registerResident'])->name('auth.register-resident');
    Route::post('/auth/login', [MobileAuthController::class, 'login'])->name('auth.login');

    Route::get('/marketplace', [MobileMarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/tenants', [MobileTenantDirectoryController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/{tenant:slug}', [MobileTenantDirectoryController::class, 'show'])->name('tenants.show');
    Route::get('/tenants/{tenant:slug}/home', [MobileTenantDirectoryController::class, 'home'])->name('tenants.home');
    Route::get('/units/{unit:code}', [MobileUnitController::class, 'show'])->name('units.show');

    Route::middleware(['auth:sanctum', 'mobile.tenant'])->group(function () {
        Route::get('/auth/me', [MobileAuthController::class, 'me'])->name('auth.me');
        Route::post('/auth/logout', [MobileAuthController::class, 'logout'])->name('auth.logout');

        Route::get('/dashboard', [MobileDashboardController::class, 'show'])->name('dashboard.show');

        Route::get('/units/meta', [MobileUnitController::class, 'meta'])->name('units.meta');
        Route::get('/units', [MobileUnitController::class, 'index'])->name('units.index');
        Route::post('/units', [MobileUnitController::class, 'store'])->name('units.store');
        Route::match(['put', 'patch'], '/units/{unit:code}', [MobileUnitController::class, 'update'])->name('units.update');
    });
});
