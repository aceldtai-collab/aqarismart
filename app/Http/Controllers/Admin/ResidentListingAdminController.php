<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdDuration;
use App\Models\City;
use App\Models\ResidentListing;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResidentListingAdminController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('moderate', new ResidentListing);

        $query = ResidentListing::with(['user', 'subcategory', 'city', 'adDuration'])
            ->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($adStatus = $request->query('ad_status')) {
            $query->where('ad_status', $adStatus);
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereJsonContains('title->en', $search)
                  ->orWhereJsonContains('title->ar', $search);
            });
        }

        $listings = $query->paginate(20)->withQueryString();

        return view('admin.resident-listings.index', compact('listings'));
    }

    public function show(ResidentListing $residentListing): View
    {
        $this->authorize('moderate', $residentListing);

        $residentListing->load(['user', 'subcategory', 'city', 'area', 'adDuration', 'moderator']);

        return view('admin.resident-listings.show', compact('residentListing'));
    }

    public function edit(ResidentListing $residentListing): View
    {
        $this->authorize('moderate', $residentListing);

        $residentListing->load(['user', 'subcategory', 'city', 'adDuration']);
        $subcategories = Subcategory::orderBy('name')->get();
        $cities = City::where('is_active', true)->orderBy('name_en')->get();
        $adDurations = AdDuration::active()->ordered()->get();

        return view('admin.resident-listings.edit', compact('residentListing', 'subcategories', 'cities', 'adDurations'));
    }

    public function update(Request $request, ResidentListing $residentListing): RedirectResponse
    {
        $this->authorize('moderate', $residentListing);

        $data = $request->validate([
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.ar' => ['nullable', 'string', 'max:255'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'listing_type' => ['nullable', 'in:sale,rent'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'numeric', 'min:0'],
            'area_m2' => ['nullable', 'numeric', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'status' => ['nullable', 'in:active,pending,rejected,suspended'],
            'ad_status' => ['nullable', 'in:active,pending,expired'],
            'ad_duration_id' => ['nullable', 'exists:ad_durations,id'],
            'moderation_notes' => ['nullable', 'string'],
        ]);

        $residentListing->update($data);

        return redirect()->route('admin.resident-listings.show', $residentListing)
            ->with('status', __('Listing updated successfully.'));
    }

    public function destroy(ResidentListing $residentListing): RedirectResponse
    {
        $this->authorize('moderate', $residentListing);

        $residentListing->delete();

        return redirect()->route('admin.resident-listings.index')
            ->with('status', __('Listing deleted successfully.'));
    }

    public function approve(ResidentListing $residentListing): RedirectResponse
    {
        $this->authorize('moderate', $residentListing);

        $residentListing->moderate('active', null, auth()->id());

        if ($residentListing->ad_status === 'pending' && $residentListing->payment_status === 'paid') {
            $residentListing->startAd();
        }

        return redirect()->route('admin.resident-listings.show', $residentListing)
            ->with('status', __('Listing approved.'));
    }

    public function reject(Request $request, ResidentListing $residentListing): RedirectResponse
    {
        $this->authorize('moderate', $residentListing);

        $request->validate([
            'moderation_notes' => ['nullable', 'string'],
        ]);

        $residentListing->moderate('rejected', $request->input('moderation_notes'), auth()->id());

        return redirect()->route('admin.resident-listings.show', $residentListing)
            ->with('status', __('Listing rejected.'));
    }
}
