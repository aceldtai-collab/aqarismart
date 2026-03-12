<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Central routes (no locale prefix)
require __DIR__.'/auth.php';

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
