<?php

use App\Http\Controllers\PublicHomeController;
use Illuminate\Support\Facades\Route;

// Public marketing site (restrict to base domain only)
Route::domain(config('tenancy.base_domain'))
    ->group(function () {
        Route::view('/book-call', 'public.book-call')->name('book-call');

        // Marketplace page
        Route::get('/marketplace', [PublicHomeController::class, 'marketplace'])->name('public.marketplace');

        // Public unit search
        Route::get('/search', [PublicHomeController::class, 'search'])->name('public.search');
        Route::get('/explore', function () {
            return redirect()->route('public.search', request()->query());
        })->name('public.search.legacy');

        // Legacy localized paths -> query param
        Route::get('/{locale}/book-call', function (string $locale) {
            abort_unless(in_array($locale, ['en', 'ar'], true), 404);
            return redirect()->route('book-call', ['lang' => $locale]);
        })->where('locale', 'en|ar')->name('book-call.localized');

        Route::get('/{locale}/marketplace', function (string $locale) {
            abort_unless(in_array($locale, ['en', 'ar'], true), 404);
            return redirect()->route('public.marketplace', ['lang' => $locale]);
        })->where('locale', 'en|ar')->name('public.marketplace.localized');

        Route::get('/{locale}/search', function (string $locale) {
            abort_unless(in_array($locale, ['en', 'ar'], true), 404);
            return redirect()->route('public.search', ['lang' => $locale]);
        })->where('locale', 'en|ar')->name('public.search.localized');

        Route::get('/{locale}', function (string $locale) {
            abort_unless(in_array($locale, ['en', 'ar'], true), 404);
            return redirect()->route('public.marketplace', ['lang' => $locale]);
        })->where('locale', 'en|ar')->name('home.localized');

        // Default home
        Route::get('/', function () {
            return redirect()->route('public.marketplace');
        })->name('home');
    })->middleware(['web']);
