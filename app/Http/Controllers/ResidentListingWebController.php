<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResidentListingRequest;
use App\Models\AdDuration;
use App\Models\City;
use App\Models\ResidentListing;
use App\Models\State;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResidentListingWebController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $listings = ResidentListing::with(['city', 'area', 'subcategory.category', 'adDuration'])
            ->forUser($user->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                return $request->string('status')->toString() === 'expired'
                    ? $query->expired()
                    : $query->where('status', $request->string('status')->toString());
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => ResidentListing::forUser($user->id)->count(),
            'active' => ResidentListing::forUser($user->id)->active()->count(),
            'expired' => ResidentListing::forUser($user->id)->expired()->count(),
            'expiring_soon' => ResidentListing::forUser($user->id)->expiringSoon(2)->count(),
        ];

        return view('resident-listings.index', compact('listings', 'stats'));
    }

    public function create(): View
    {
        return view('resident-listings.create', $this->formData());
    }

    public function store(StoreResidentListingRequest $request): RedirectResponse
    {
        $this->authorize('create', ResidentListing::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['code'] = ResidentListing::generateCode();
        $data['currency'] = $data['currency'] ?? 'IQD';
        $data['source'] = 'direct_owner';
        $data['payment_status'] = 'paid';
        $data['payment_method'] = $data['payment_method'] ?? 'manual';
        $data['status'] = 'active';
        $data['photos'] = $this->storeUploadedPhotos($request->file('photos', []));

        if (!empty($data['location_url'])) {
            $coords = $this->parseLocationUrl($data['location_url']);
            if ($coords) {
                $data['lat'] = $coords['lat'];
                $data['lng'] = $coords['lng'];
            }
        }

        if ($duration = AdDuration::find($data['ad_duration_id'])) {
            $data['ad_started_at'] = now();
            $data['ad_expires_at'] = now()->addDays($duration->days);
            $data['ad_status'] = 'active';
            $data['amount_paid'] = $duration->price;
            $data['paid_at'] = now();
        } else {
            $data['ad_status'] = 'pending';
        }

        $listing = ResidentListing::create($data);

        return redirect()
            ->route('my-listings.index')
            ->with('status', 'listing-created');
    }

    public function edit(Request $request, string $code): View
    {
        $residentListing = ResidentListing::where('code', $code)->firstOrFail();
        $this->authorize('update', $residentListing);

        return view('resident-listings.edit', [
            'residentListing' => $residentListing->load(['city', 'area', 'subcategory.category', 'adDuration']),
        ] + $this->formData());
    }

    public function update(StoreResidentListingRequest $request, string $code): RedirectResponse
    {
        $residentListing = ResidentListing::where('code', $code)->firstOrFail();
        $this->authorize('update', $residentListing);

        $data = $request->validated();
        $existingPhotos = array_values($residentListing->photos ?? []);
        $keptPhotos = collect($request->input('existing_photos', []))
            ->filter(fn ($photo) => in_array($photo, $existingPhotos, true))
            ->values()
            ->all();

        $this->deleteRemovedPhotos($existingPhotos, $keptPhotos);
        $data['photos'] = array_values(array_merge($keptPhotos, $this->storeUploadedPhotos($request->file('photos', []))));

        if (!empty($data['location_url'])) {
            $coords = $this->parseLocationUrl($data['location_url']);
            if ($coords) {
                $data['lat'] = $coords['lat'];
                $data['lng'] = $coords['lng'];
            }
        }

        $residentListing->update($data);

        return redirect()
            ->route('resident-listings.web.show', $residentListing->code)
            ->with('status', 'listing-updated');
    }

    public function destroy(Request $request, string $code): RedirectResponse
    {
        $residentListing = ResidentListing::where('code', $code)->firstOrFail();
        $this->authorize('delete', $residentListing);

        $this->deleteRemovedPhotos($residentListing->photos ?? [], []);
        $residentListing->delete();

        return redirect()
            ->route('my-listings.index')
            ->with('status', 'listing-deleted');
    }

    public function show(string $code): View
    {
        $residentListing = ResidentListing::with(['user', 'city', 'area', 'subcategory.category', 'adDuration'])
            ->where('code', $code)
            ->firstOrFail();

        $viewer = request()->user();
        $isOwner = $viewer && (int) $viewer->id === (int) $residentListing->user_id;
        $isPubliclyVisible = $residentListing->status === 'active'
            && $residentListing->ad_status === 'active'
            && ! $residentListing->is_expired;

        abort_unless($isOwner || $isPubliclyVisible, 404);

        return view('resident-listings.show', compact('residentListing'));
    }

    protected function formData(): array
    {
        return [
            'subcategories' => Subcategory::query()->orderBy('name')->get(['id', 'name', 'category_id']),
            'cities' => City::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en', 'name_ar']),
            'areas' => State::query()->where('is_active', true)->orderBy('name_en')->get(['id', 'name_en', 'name_ar']),
            'adDurations' => AdDuration::query()->orderBy('days')->get(),
        ];
    }

    protected function storeUploadedPhotos(array $files): array
    {
        $photos = [];

        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $path = $file->store('resident_listings', 'public');
                if ($path) {
                    $photos[] = Storage::url($path);
                }
            }
        }

        return array_values($photos);
    }

    protected function deleteRemovedPhotos(array $existingPhotos, array $keptPhotos): void
    {
        foreach ($existingPhotos as $photo) {
            if (!in_array($photo, $keptPhotos, true) && is_string($photo) && Str::startsWith($photo, '/storage/')) {
                Storage::disk('public')->delete(Str::after($photo, '/storage/'));
            }
        }
    }

    protected function parseLocationUrl(string $url): ?array
    {
        $patterns = [
            '/q=(-?\d+\.?\d*),(-?\d+\.?\d*)/',
            '/@(-?\d+\.?\d*),(-?\d+\.?\d*)/',
            '/ll=(-?\d+\.?\d*),(-?\d+\.?\d*)/',
            '/mlat=(-?\d+\.?\d*)&mlon=(-?\d+\.?\d*)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $match)) {
                return ['lat' => (float) $match[1], 'lng' => (float) $match[2]];
            }
        }

        return null;
    }
}
