<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Middleware\SubstituteBindings;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TenantAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\LandingSettingsController;
use App\Http\Controllers\Admin\PropertyAdminController;
use App\Http\Controllers\Admin\UnitAdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\AttributeFieldController;
use App\Http\Controllers\Admin\ReportController;

use App\Http\Controllers\Admin\AgentAdminController;
use App\Http\Controllers\Admin\ContactAdminController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\Admin\TenantSubscriptionController;

// /admin/* (no locale prefix)
Route::middleware(['web', 'auth', 'verified', 'superadmin', SubstituteBindings::class])
    ->prefix('admin')
    ->as('admin.')
    ->scopeBindings()
    ->group(function () {

                // Dashboard & lists
                Route::get('/', AdminDashboardController::class)->name('index');
                Route::get('/reports', ReportController::class)->name('reports.index');
                Route::get('/tenants', [TenantAdminController::class, 'index'])->name('tenants.index');
                Route::get('/tenants/{tenant:id}', [TenantAdminController::class, 'show'])
                    ->whereNumber('tenant')
                    ->name('tenants.show');

                Route::get('/users', [UserAdminController::class, 'index'])->name('users.index');
                Route::get('/properties', [PropertyAdminController::class, 'index'])->name('properties.index');
                Route::get('/units', [UnitAdminController::class, 'index'])->name('units.index');

                // Landing settings
                Route::prefix('settings/landing')->as('settings.landing.')->group(function () {
                    Route::get('/', [LandingSettingsController::class, 'edit'])->name('edit');
                    Route::post('/', [LandingSettingsController::class, 'update'])->name('update');
                    Route::post('/section/{section}', [LandingSettingsController::class, 'updateSection'])->name('updateSection');
                });

                // Agents (central, superadmin)
                Route::pattern('agent', '[0-9]+');
                Route::resource('agents', AgentAdminController::class)
                    ->parameters(['agents' => 'agent'])
                    ->except(['show'])
                    ->names('agents');

                // Contacts (central, superadmin)
                Route::pattern('contact', '[0-9]+');
                Route::resource('contacts', ContactAdminController::class)
                    ->parameters(['contacts' => 'contact'])
                    ->except(['show'])
                    ->names('contacts');

                Route::get('contacts-import', [ContactAdminController::class, 'importForm'])->name('contacts.import.form');
                Route::post('contacts-import', [ContactAdminController::class, 'importStore'])->name('contacts.import.store');

                // Categories
                Route::resource('categories', CategoryController::class)
                    ->parameters(['categories' => 'category'])
                    ->except(['show'])
                    ->names('categories');

                // Subcategories
                Route::resource('subcategories', SubcategoryController::class)
                    ->parameters(['subcategories' => 'subcategory'])
                    ->except(['show'])
                    ->names('subcategories');

                // Attribute Fields
                Route::resource('attribute-fields', AttributeFieldController::class)
                    ->parameters(['attribute-fields' => 'attributeField'])
                    ->except(['show'])
                    ->names('attribute-fields');

                Route::resource('users', UserAdminController::class)
                    ->parameters(['users' => 'user'])
                    ->except(['show'])
                    ->names('users');

                // Packages
                Route::resource('packages', PackageController::class)
                    ->parameters(['packages' => 'package'])
                    ->except(['show'])
                    ->names('packages');

                // Add-ons
                Route::resource('addons', AddonController::class)
                    ->parameters(['addons' => 'addon'])
                    ->except(['show'])
                    ->names('addons');

                // Tenant subscription management
                Route::get('/tenants/{tenant}/subscription', [TenantSubscriptionController::class, 'show'])
                    ->whereNumber('tenant')
                    ->name('tenants.subscription');
                Route::post('/tenants/{tenant}/subscription', [TenantSubscriptionController::class, 'subscribe'])
                    ->whereNumber('tenant')
                    ->name('tenants.subscription.subscribe');
                Route::delete('/tenants/{tenant}/subscription', [TenantSubscriptionController::class, 'cancel'])
                    ->whereNumber('tenant')
                    ->name('tenants.subscription.cancel');
                Route::post('/tenants/{tenant}/addons', [TenantSubscriptionController::class, 'attachAddon'])
                    ->whereNumber('tenant')
                    ->name('tenants.addons.attach');
                Route::delete('/tenants/{tenant}/addons/{tenantAddon}', [TenantSubscriptionController::class, 'removeAddon'])
                    ->whereNumber('tenant')
                    ->name('tenants.addons.remove');
    });
