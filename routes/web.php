<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobileAppController;

// Central routes (no locale prefix)
require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect('/mobile/marketplace');
});

Route::prefix('mobile')->name('mobile.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('mobile.marketplace');
    })->name('home');
    Route::get('/login', function () {
        return view('mobile.auth.login');
    })->name('login');
    Route::get('/register', function () {
        return view('mobile.auth.register');
    })->name('register');
    Route::get('/marketplace', [MobileAppController::class, 'marketplace'])->name('marketplace');
    Route::get('/dashboard', [MobileAppController::class, 'dashboard'])->name('dashboard');
    Route::get('/units', [MobileAppController::class, 'units'])->name('units.index');
    Route::get('/units/create', [MobileAppController::class, 'createUnit'])->name('units.create');
    Route::get('/units/{unit:code}', [MobileAppController::class, 'showUnit'])->name('units.show');
    Route::get('/units/{unit:code}/edit', [MobileAppController::class, 'editUnit'])->name('units.edit');
    Route::get('/tenants', [MobileAppController::class, 'tenants'])->name('tenants.index');
    Route::get('/tenants/{tenant:slug}', [MobileAppController::class, 'showTenant'])->name('tenants.show');
    Route::get('/profile', [MobileAppController::class, 'profile'])->name('profile');
    Route::get('/about', [MobileAppController::class, 'about'])->name('about');
});

// Tenant Switcher (central, requires auth)
Route::post('/tenant/switch', function (\Illuminate\Http\Request $request) {
    $request->validate(['tenant_id' => ['required','integer']]);
    $tenant = \App\Models\Tenant::findOrFail($request->integer('tenant_id'));
    if (! auth()->user()->tenants()->whereKey($tenant->id)->exists()) {
        abort(403);
    }
    $url = app(\App\Services\Tenancy\TenantManager::class)->tenantUrl($tenant, '/dashboard');
    return redirect()->away($url);
})->middleware(['auth'])->name('tenant.switch');

// Docs (public)
Route::get('/docs/public-landing-content', function () {
    $path = base_path('docs/public-landing-content.md');
    abort_unless(is_file($path), 404);

    return response()->file($path, [
        'Content-Type' => 'text/markdown; charset=utf-8',
    ]);
})->name('docs.public-landing');

// Public sales story/flow (base domain)
Route::view('/sales-story', 'tenant.sales-story')->name('sales-story');
Route::view('/sales-st', 'tenant.sales-story')->name('sales-story.short');
Route::view('/sales-flow', 'tenant.sales-flow')->name('sales-flow');
Route::view('/sales-flow/print', 'tenant.sales-flow-print')->name('sales-flow.print');

// Central Authenticated
Route::middleware('auth')->group(function () {
    // Staff profile (admin/app layout)
    Route::middleware(['tenant'])->get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Resident profile (public layout)
    Route::middleware(['tenant','resident'])->get('/resident/profile', [ProfileController::class, 'resident'])->name('resident.profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
