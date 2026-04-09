<?php

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileDashboardController;
use App\Http\Controllers\Api\MobileMarketplaceController;
use App\Http\Controllers\Api\MobileTenantDirectoryController;
use App\Http\Controllers\Api\MobileUnitController;
use App\Http\Controllers\Api\ResidentListingController;
use App\Models\AdDuration;
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

    // Resident Listings - Public routes
    Route::get('/resident-listings', [ResidentListingController::class, 'index'])->name('resident-listings.index');
    Route::get('/resident-listings/{residentListing:code}', [ResidentListingController::class, 'show'])->name('resident-listings.show');
    
    // Ad Durations - Public route
    Route::get('/ad-durations', function () {
        return response()->json([
            'data' => AdDuration::active()->ordered()->get()->map(fn($duration) => [
                'id' => $duration->id,
                'name_en' => $duration->name_en,
                'name_ar' => $duration->name_ar,
                'days' => $duration->days,
                'price' => (float) $duration->price,
                'currency' => $duration->currency,
                'formatted_price' => $duration->formatted_price,
            ])
        ]);
    })->name('ad-durations.index');

    // Resident Listings - Session auth for web users
    Route::middleware(['web', 'auth'])->group(function () {
        Route::post('/resident-listings/web', [ResidentListingController::class, 'storeWeb'])->name('resident-listings.store.web');
    });

    Route::middleware(['auth:sanctum', 'mobile.tenant'])->group(function () {
        Route::get('/auth/me', [MobileAuthController::class, 'me'])->name('auth.me');
        Route::post('/auth/web-dashboard-link', [MobileAuthController::class, 'webDashboardLink'])->name('auth.web-dashboard-link');
        Route::post('/auth/logout', [MobileAuthController::class, 'logout'])->name('auth.logout');

        Route::get('/dashboard', [MobileDashboardController::class, 'show'])->name('dashboard.show');

        Route::get('/units/meta', [MobileUnitController::class, 'meta'])->name('units.meta');
        Route::get('/units', [MobileUnitController::class, 'index'])->name('units.index');
        Route::post('/units', [MobileUnitController::class, 'store'])->name('units.store');
        Route::match(['put', 'patch'], '/units/{unit:code}', [MobileUnitController::class, 'update'])->name('units.update');

        // Resident Listings - Authenticated user routes
        Route::get('/my-listings', [ResidentListingController::class, 'myListings'])->name('my-listings.index');
        Route::get('/my-listings/expiring-soon', [ResidentListingController::class, 'expiringSoon'])->name('my-listings.expiring-soon');
        Route::post('/resident-listings', [ResidentListingController::class, 'store'])->name('resident-listings.store');
        Route::match(['put', 'patch'], '/resident-listings/{residentListing}', [ResidentListingController::class, 'update'])->name('resident-listings.update');
        Route::delete('/resident-listings/{residentListing}', [ResidentListingController::class, 'destroy'])->name('resident-listings.destroy');
        Route::post('/resident-listings/{residentListing}/renew', [ResidentListingController::class, 'renew'])->name('resident-listings.renew');
    });
});
