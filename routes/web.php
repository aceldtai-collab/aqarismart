<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobileAppController;
use App\Http\Controllers\Api\MobileAuthController;

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
    Route::get('/search', [MobileAppController::class, 'search'])->name('search');
    Route::get('/dashboard', [MobileAppController::class, 'dashboard'])->name('dashboard');
    Route::get('/units', [MobileAppController::class, 'units'])->name('units.index');
    Route::get('/units/create', [MobileAppController::class, 'createUnit'])->name('units.create');
    Route::get('/units/{unit}', [MobileAppController::class, 'showUnit'])->name('units.show');
    Route::get('/units/{unit}/edit', [MobileAppController::class, 'editUnit'])->name('units.edit');
    Route::get('/tenants', [MobileAppController::class, 'tenants'])->name('tenants.index');
    Route::get('/tenants/{tenant}/search', [MobileAppController::class, 'tenantSearch'])->name('tenants.search');
    Route::get('/tenants/{tenant}', [MobileAppController::class, 'showTenant'])->name('tenants.show');
    Route::get('/profile', [MobileAppController::class, 'profile'])->name('profile');
    Route::get('/about', [MobileAppController::class, 'about'])->name('about');
    
    // Resident Listings
    Route::get('/my-listings', [MobileAppController::class, 'myListings'])->name('my-listings.index');
    Route::get('/my-listings/create', [MobileAppController::class, 'createListing'])->name('my-listings.create');
    Route::get('/my-listings/{residentListing}/edit', [MobileAppController::class, 'editListing'])->name('my-listings.edit');
    Route::get('/resident-listings/{residentListing}', [MobileAppController::class, 'showListing'])->name('resident-listings.show');
});

Route::get('/mobile/auth/web-dashboard/{nonce}', [MobileAuthController::class, 'openWebDashboard'])
    ->middleware(['web', 'signed'])
    ->name('mobile.auth.web-dashboard');

// Session-auth JSON endpoints (for web-browser residents who have no Sanctum token)
Route::prefix('api/mobile/web')->name('api.mobile.web.')->middleware(['web', 'auth'])->group(function () {
    Route::get('/me', function () {
        $user = auth()->user();
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'phone_country_code' => $user->phone_country_code,
                'email_verified_at' => $user->email_verified_at,
                'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames() : [],
                'tenants' => $user->tenants()->get(['tenants.id', 'tenants.name', 'tenants.slug', 'tenant_user.role'])->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'slug' => $t->slug,
                    'pivot' => ['role' => $t->pivot->role ?? null],
                ]),
            ],
            'current_tenant' => null,
            'tenant_role' => null,
        ]);
    })->name('me');

    Route::get('/my-listings', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $query = \App\Models\ResidentListing::with(['city', 'area', 'subcategory.category', 'adDuration'])
            ->forUser($user->id);
        if ($request->filled('status')) {
            if ($request->input('status') === 'expired') {
                $query->expired();
            } else {
                $query->where('status', $request->input('status'));
            }
        }
        $listings = $query->latest()->paginate(20);
        return response()->json([
            'data' => \App\Http\Resources\MobileResidentListingResource::collection($listings->getCollection()),
            'meta' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ],
            'stats' => [
                'total' => \App\Models\ResidentListing::forUser($user->id)->count(),
                'active' => \App\Models\ResidentListing::forUser($user->id)->active()->count(),
                'expired' => \App\Models\ResidentListing::forUser($user->id)->expired()->count(),
                'expiring_soon' => \App\Models\ResidentListing::forUser($user->id)->expiringSoon(2)->count(),
            ],
        ]);
    })->name('my-listings');
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

// NativePHP: run missing migrations on-demand (safe to call multiple times)
Route::get('/mobile/run-migrations', function () {
    try {
        \App\Services\NativePHP\MigrationHelper::runMigrations();
        // Seed ad_durations if empty
        if (\Illuminate\Support\Facades\Schema::hasTable('ad_durations') && \DB::table('ad_durations')->count() === 0) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'AdDurationSeeder', '--force' => true]);
        }
        return response()->json(['ok' => true, 'message' => 'Migrations ran successfully']);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
    }
})->name('mobile.run-migrations');

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
