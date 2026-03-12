<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TenantHomeController extends Controller
{
    public function __construct(protected TenantManager $tenants) {}

    public function index(Request $request): View|RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $beds = (int) $request->query('beds', 0);
        $baths = (float) $request->query('baths', 0);
        $max = (int) $request->query('max', 0); // in USD per month
        $category = (int) $request->query('category', 0);
        $subcategory = (int) $request->query('subcategory', 0);
        $q = trim((string) $request->query('q', ''));
        $rawListingType = trim((string) $request->query('listing_type', ''));
        $listing_type = in_array($rawListingType, Unit::LISTING_TYPES, true) ? $rawListingType : Unit::LISTING_RENT;
        $hasListingTypeFilter = $rawListingType !== '';

        // If any filters/search are applied on home, redirect to the dedicated search page
        $hasFilters = ($beds > 0) || ($baths > 0) || ($max > 0) || ($category > 0) || ($subcategory > 0) || ($q !== '') || $hasListingTypeFilter;
        if ($hasFilters) {
            return redirect()->route('tenant.search', $request->query());
        }

        $unitsQuery = Unit::with(['property','subcategory.category','agents','agent'])
            ->whereIn('status', ['vacant', 'occupied'])
            ->where('listing_type', $listing_type)
            ->when($beds > 0, fn ($q2) => $q2->where('beds', '>=', $beds))
            ->when($baths > 0, fn ($q2) => $q2->where('baths', '>=', $baths))
            ->when($max > 0, fn ($q2) => $q2->where('market_rent', '<=', $max * 100))
            ->when($subcategory > 0, fn ($q2) => $q2->where('subcategory_id', $subcategory))
            ->when($category > 0, function ($q2) use ($category) {
                $q2->where(function ($inner) use ($category) {
                    $inner->whereHas('property', fn($qq) => $qq->where('category_id', $category))
                        ->orWhereHas('subcategory.category', fn($qq) => $qq->where('id', $category));
                });
            })
            ->when($q !== '', function ($q2) use ($q) {
                $q2->where(function ($inner) use ($q) {
                    $inner->whereHas('property', function ($qq) use ($q) {
                        $qq->where('name', 'like', "%$q%");
                    })->orWhere('code', 'like', "%$q%")
                      ->orWhere('title', 'like', "%$q%");
                });
            });

        $units = (clone $unitsQuery)
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        // Build list of subcategories that have available units under current filters (excluding subcategory filter)
        $availableSubcategoryIds = (clone $unitsQuery)
            ->when($subcategory > 0, fn($q3) => $q3) // ignore current subcategory filter to show options
            ->whereNotNull('subcategory_id')
            ->distinct()
            ->pluck('subcategory_id');

        $categories = \App\Models\Category::with('subcategories')->orderBy('name')->get();
        $types = \App\Models\Subcategory::whereIn('id', $availableSubcategoryIds)->orderBy('name')->get();

        // Fake popular cities data (placeholder)
        $popularCities = [
            ['name' => 'Irbid', 'count' => 942, 'image' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?q=80&w=1200&auto=format&fit=crop'],
            ['name' => 'Irbid', 'count' => 942, 'image' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?q=80&w=1200&auto=format&fit=crop'],
            ['name' => 'Aqaba', 'count' => 503, 'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1200&auto=format&fit=crop'],
            ['name' => 'Zarqa', 'count' => 321, 'image' => 'https://images.unsplash.com/photo-1505764706515-aa95265c5abc?q=80&w=1200&auto=format&fit=crop'],
            ['name' => 'Irbid', 'count' => 942, 'image' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?q=80&w=1200&auto=format&fit=crop'],
        ];
        
        return view('tenant.home', compact('tenant', 'units', 'beds', 'baths', 'max', 'category', 'subcategory', 'q', 'categories', 'types', 'popularCities', 'listing_type'));
    }

    public function search(Request $request): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $beds = (int) $request->query('beds', 0);
        $baths = (float) $request->query('baths', 0);
        $max = (int) $request->query('max', 0); // in USD per month
        $category = (int) $request->query('category', 0);
        $subcategory = (int) $request->query('subcategory', 0);
        $q = trim((string) $request->query('q', ''));
        $rawListingType = trim((string) $request->query('listing_type', ''));
        $listing_type = in_array($rawListingType, Unit::LISTING_TYPES, true) ? $rawListingType : Unit::LISTING_RENT;

        $unitsQuery = Unit::with(['property','subcategory.category','agents','agent'])
            ->whereIn('status', ['vacant', 'occupied'])
            ->where('listing_type', $listing_type)
            ->when($beds > 0, fn ($q2) => $q2->where('beds', '>=', $beds))
            ->when($baths > 0, fn ($q2) => $q2->where('baths', '>=', $baths))
            ->when($max > 0, fn ($q2) => $q2->where('market_rent', '<=', $max * 100))
            ->when($subcategory > 0, fn ($q2) => $q2->where('subcategory_id', $subcategory))
            ->when($category > 0, function ($q2) use ($category) {
                $q2->where(function ($inner) use ($category) {
                    $inner->whereHas('property', fn($qq) => $qq->where('category_id', $category))
                        ->orWhereHas('subcategory.category', fn($qq) => $qq->where('id', $category));
                });
            })
            ->when($q !== '', function ($q2) use ($q) {
                $q2->where(function ($inner) use ($q) {
                    $inner->whereHas('property', function ($qq) use ($q) {
                        $qq->where('name', 'like', "%$q%");
                    })->orWhere('code', 'like', "%$q%")
                      ->orWhere('title', 'like', "%$q%");
                });
            });

        $units = (clone $unitsQuery)
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $categories = \App\Models\Category::orderBy('name')->get();
        $subcategories = \App\Models\Subcategory::when($category > 0, fn($q3) => $q3->where('category_id', $category))->orderBy('name')->get();
        return view('tenant.search', compact('tenant', 'units', 'beds', 'baths', 'max', 'category', 'subcategory', 'q', 'listing_type') + [
            'subcategories' => $subcategories,
        ]);
    }
}
