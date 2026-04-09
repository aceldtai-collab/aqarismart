<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResidentListingRequest;
use App\Http\Resources\MobileResidentListingResource;
use App\Models\ResidentListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResidentListingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ResidentListing::with(['user', 'city', 'area', 'subcategory.category', 'adDuration'])
            ->active();

        if ($request->filled('listing_type')) {
            $query->byListingType($request->input('listing_type'));
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }

        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->input('subcategory_id'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', (int) $request->input('bedrooms'));
        }

        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $listings = $query->paginate((int) $request->input('per_page', 12))->withQueryString();

        return response()->json([
            'data' => MobileResidentListingResource::collection($listings->getCollection()),
            'meta' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ],
            'filters' => $request->only(['listing_type', 'city_id', 'subcategory_id', 'min_price', 'max_price', 'bedrooms', 'sort']),
        ]);
    }

    public function myListings(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $query = ResidentListing::with(['city', 'area', 'subcategory.category', 'adDuration'])
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
            'data' => MobileResidentListingResource::collection($listings->getCollection()),
            'meta' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ],
            'stats' => [
                'total' => ResidentListing::forUser($user->id)->count(),
                'active' => ResidentListing::forUser($user->id)->active()->count(),
                'expired' => ResidentListing::forUser($user->id)->expired()->count(),
                'expiring_soon' => ResidentListing::forUser($user->id)->expiringSoon(2)->count(),
            ],
        ]);
    }

    public function show(string $code): JsonResponse
    {
        $listing = ResidentListing::with(['user', 'city', 'area', 'subcategory.category', 'adDuration'])
            ->where('code', $code)
            ->firstOrFail();

        return response()->json([
            'data' => new MobileResidentListingResource($listing),
        ]);
    }

    public function store(StoreResidentListingRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $this->authorize('create', ResidentListing::class);

        $data = $request->validated();
        $data['user_id'] = $user->id;
        $data['code'] = ResidentListing::generateCode();
        $data['currency'] = $data['currency'] ?? 'IQD';
        $data['source'] = 'direct_owner';

        // Handle photos — base64 data URLs (from mobile JSON form) or multipart uploads
        if ($request->hasFile('photos')) {
            $data['photos'] = $this->storeUploadedPhotos($request->file('photos'), 'resident_listings');
        } elseif (!empty($data['photos']) && is_array($data['photos'])) {
            $data['photos'] = $this->storeBase64Photos($data['photos'], 'resident_listings');
        }

        // Parse location URL for coordinates if provided
        if (!empty($data['location_url'])) {
            $coords = $this->parseLocationUrl($data['location_url']);
            if ($coords) {
                $data['lat'] = $coords['lat'];
                $data['lng'] = $coords['lng'];
            }
        }

        $listing = ResidentListing::create($data);

        // Load relationships for response
        $listing->load(['city', 'area', 'subcategory.category', 'adDuration']);

        return response()->json([
            'message' => 'Listing created successfully',
            'data' => new MobileResidentListingResource($listing),
        ], 201);
    }

    public function storeWeb(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $this->authorize('create', ResidentListing::class);

        $data = $request->validate([
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.ar' => ['nullable', 'string', 'max:255'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'subcategory_id' => ['required', 'exists:subcategories,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'numeric', 'min:0'],
            'area_m2' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'location' => ['nullable', 'string'],
            'location_url' => ['nullable', 'string'],
            'listing_type' => ['required', 'in:sale,rent'],
            'ad_duration_id' => ['required', 'exists:ad_durations,id'],
            'photos' => ['nullable', 'array'],
        ]);

        $data['user_id'] = $user->id;
        $data['code'] = ResidentListing::generateCode();
        $data['currency'] = $data['currency'] ?? 'IQD';
        $data['source'] = 'direct_owner';

        // Handle base64 photos from web form
        if (!empty($data['photos']) && is_array($data['photos'])) {
            $data['photos'] = $this->storeBase64Photos($data['photos'], 'resident_listings');
        }

        // Parse location URL for coordinates if provided
        if (!empty($data['location_url'])) {
            $coords = $this->parseLocationUrl($data['location_url']);
            if ($coords) {
                $data['lat'] = $coords['lat'];
                $data['lng'] = $coords['lng'];
            }
        }

        $listing = ResidentListing::create($data);
        $listing->load(['city', 'area', 'subcategory.category', 'adDuration']);

        return response()->json([
            'message' => 'Listing created successfully',
            'data' => new MobileResidentListingResource($listing),
        ], 201);
    }

    public function update(StoreResidentListingRequest $request, ResidentListing $residentListing): JsonResponse
    {
        $this->authorize('update', $residentListing);

        $data = $request->validated();

        // Handle photos — base64 data URLs or multipart uploads
        if ($request->hasFile('photos')) {
            $data['photos'] = $this->storeUploadedPhotos($request->file('photos'), 'resident_listings', $residentListing->photos ?? []);
        } elseif (!empty($data['photos']) && is_array($data['photos'])) {
            $data['photos'] = $this->storeBase64Photos($data['photos'], 'resident_listings', $residentListing->photos ?? []);
        }

        // Parse location URL for coordinates if provided
        if (!empty($data['location_url'])) {
            $coords = $this->parseLocationUrl($data['location_url']);
            if ($coords) {
                $data['lat'] = $coords['lat'];
                $data['lng'] = $coords['lng'];
            }
        }

        $residentListing->update($data);
        $residentListing->load(['city', 'area', 'subcategory.category', 'adDuration']);

        return response()->json([
            'message' => 'Listing updated successfully',
            'data' => new MobileResidentListingResource($residentListing),
        ]);
    }

    public function destroy(ResidentListing $residentListing): JsonResponse
    {
        $this->authorize('delete', $residentListing);

        $residentListing->delete();

        return response()->json([
            'message' => 'Listing deleted successfully',
        ]);
    }

    public function renew(Request $request, ResidentListing $residentListing): JsonResponse
    {
        $this->authorize('renew', $residentListing);

        $request->validate([
            'ad_duration_id' => ['required', 'exists:ad_durations,id'],
        ]);

        $residentListing->renewAd($request->input('ad_duration_id'));
        $residentListing->load(['adDuration']);

        return response()->json([
            'message' => 'Listing renewal initiated. Please complete payment to activate.',
            'data' => new MobileResidentListingResource($residentListing),
        ]);
    }

    public function markAsPaid(Request $request, ResidentListing $residentListing): JsonResponse
    {
        $this->authorize('moderate', $residentListing);

        $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'payment_reference' => ['nullable', 'string'],
        ]);

        $residentListing->markAsPaid(
            $request->input('amount'),
            $request->input('payment_method'),
            $request->input('payment_reference')
        );

        return response()->json([
            'message' => 'Payment recorded and listing activated',
            'data' => new MobileResidentListingResource($residentListing),
        ]);
    }

    public function expiringSoon(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $listings = ResidentListing::with(['city', 'area', 'subcategory.category', 'adDuration'])
            ->forUser($user->id)
            ->expiringSoon(2)
            ->latest()
            ->get();

        return response()->json([
            'data' => MobileResidentListingResource::collection($listings),
            'count' => $listings->count(),
        ]);
    }

    private function storeUploadedPhotos(array $files, string $directory, array $existing = []): array
    {
        $photos = $existing;
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $path = $file->store($directory, 'public');
                if ($path) {
                    $photos[] = Storage::url($path);
                }
            }
        }
        return array_values($photos);
    }

    private function storeBase64Photos(array $base64Photos, string $directory, array $existing = []): array
    {
        $photos = array_values(array_filter($existing, fn($p) => !str_starts_with((string) $p, 'data:')));
        foreach ($base64Photos as $photo) {
            if (!is_string($photo)) continue;
            if (str_starts_with($photo, 'data:image')) {
                try {
                    $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $photo);
                    $decoded = base64_decode($imageData, true);
                    if (!$decoded) continue;
                    $extension = 'jpg';
                    if (preg_match('/^data:image\/(\w+);base64/', $photo, $m)) {
                        $extension = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                    }
                    $filename = $directory . '/' . Str::uuid() . '.' . $extension;
                    Storage::disk('public')->put($filename, $decoded);
                    $photos[] = Storage::url($filename);
                } catch (\Throwable $e) {
                    continue;
                }
            } else {
                $photos[] = $photo;
            }
        }
        return array_values($photos);
    }

    private function parseLocationUrl(string $url): ?array
    {
        $patterns = [
            '/q=(-?\d+\.?\d*),(-?\d+\.?\d*)/',
            '/@(-?\d+\.?\d*),(-?\d+\.?\d*)/',
            '/ll=(-?\d+\.?\d*),(-?\d+\.?\d*)/',
            '/mlat=(-?\d+\.?\d*)&mlon=(-?\d+\.?\d*)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $match)) {
                return ['lat' => (float) $match[1], 'lng' => (float) $match[2]];
            }
        }

        return null;
    }
}
