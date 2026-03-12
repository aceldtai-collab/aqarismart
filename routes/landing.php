<?php

use App\Http\Controllers\PublicHomeController;
use Illuminate\Support\Facades\Route;

// Public marketing site (restrict to base domain only)
Route::domain(config('tenancy.base_domain'))
    ->group(function () {
        Route::view('/book-call', 'public.book-call')->name('book-call');

        // Public unit search / explore
        Route::get('/explore', [PublicHomeController::class, 'search'])->name('public.search');

        // Legacy localized paths -> query param
        Route::get('/{locale}/book-call', function (string $locale) {
            abort_unless(in_array($locale, ['en', 'ar'], true), 404);
            return redirect()->route('book-call', ['lang' => $locale]);
        })->where('locale', 'en|ar')->name('book-call.localized');

        Route::get('/{locale}', function (string $locale) {
            abort_unless(in_array($locale, ['en', 'ar'], true), 404);
            return redirect()->route('home', ['lang' => $locale]);
        })->where('locale', 'en|ar')->name('home.localized');

        // Default home
        Route::get('/', [PublicHomeController::class, 'index'])->name('home');
    })->middleware(['web']);
