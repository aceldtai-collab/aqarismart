<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\StoresPhotos;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Resources\MobileUnitResource;
use App\Models\Agent;
use App\Models\AttributeField;
use App\Models\Category;
use App\Models\City;
use App\Models\Property;
use App\Models\Subcategory;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\UnitAttribute;
use App\Services\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileUnitController extends Controller
{
    use StoresPhotos;

    public function __construct(protected TenantManager $tenants)
    {
    }

    public function meta(Request $request): JsonResponse
    {
        $tenant = $this->tenant($request);
        $user = $request->user();

        $properties = Property::query()
            ->when($user?->agent_id, fn ($query) => $query->where('agent_id', $user->agent_id))
            ->orderBy('name')
            ->get(['id', 'name', 'category_id']);

        $categories = Category::with('subcategories')->orderBy('name')->get();
        $agents = Agent::query()->orderBy('name')->get(['id', 'name', 'email', 'phone']);
        $cities = City::where('is_active', true)->orderBy('name_en')->get(['id', 'name_en', 'name_ar']);
        $attributeFields = $tenant
            ? AttributeField::with('subcategory')->availableTo($tenant->id)->orderBy('sort')->get()
            : AttributeField::with('subcategory')->global()->orderBy('sort')->get();

        return response()->json([
            'properties' => $properties,
            'categories' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'subcategories' => $category->subcategories->map(fn ($subcategory) => [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                        'category_id' => $subcategory->category_id,
                    ])->values(),
                ];
            })->values(),
            'subcategories' => Subcategory::orderBy('name')->get(['id', 'name', 'category_id']),
            'agents' => $agents,
            'cities' => $cities,
            'attribute_fields' => $attributeFields,
            'statuses' => Unit::statusLabels(),
            'listing_types' => Unit::listingTypeLabels(),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $tenant = $this->tenant($request);
        abort_if(! $tenant, 404, 'No tenant context');
        $user = $request->user();

        $query = Unit::with([
            'property.agent',
            'property.agents',
            'agent',
            'agents',
            'subcategory.category',
            'tenant',
            'city',
            'officialInfo',
            'owner',
        ]);

        $query = $this->applyUnitSearch($query, trim((string) $request->query('q', '')));
        $query = $this->applyUnitSubcategoryFilter($query, (int) $request->query('subcategory_id', 0));
        $query = $this->applyUnitPropertyFilter($query, (int) $request->query('property_id', 0));
        $query = $this->applyUnitStatusFilter($query, trim((string) $request->query('status', '')));
        $query = $this->applyUnitListingTypeFilter($query, trim((string) $request->query('listing_type', '')));
        $query = $this->applyUnitAgentFilter($query, (int) $request->query('agent_id', 0));
        $query = $this->applyUnitPriceFilter($query, (float) $request->query('min_price', 0), (float) $request->query('max_price', 0));
        $query = $this->applyUnitBedsFilter($query, (int) $request->query('beds', 0));
        $query = $this->applyUnitBathsFilter($query, (float) $request->query('baths', 0));

        if ($user?->agent_id) {
            $query->forAgent($user->agent_id);
        }

        $units = $query->orderByDesc('id')->paginate((int) $request->input('per_page', 15));

        return response()->json([
            'data' => MobileUnitResource::collection($units->getCollection()),
            'meta' => [
                'current_page' => $units->currentPage(),
                'last_page' => $units->lastPage(),
                'per_page' => $units->perPage(),
                'total' => $units->total(),
            ],
            'tenant_id' => $tenant?->id,
        ]);
    }

    public function show(Request $request, Unit $unit): JsonResponse
    {
        $unit->load([
            'property.agent',
            'property.agents',
            'agent',
            'agents',
            'subcategory.category',
            'city',
            'area',
            'unitAttributes.attributeField',
            'officialInfo',
            'owner',
            'tenant.activeSubscription.package',
        ]);

        return response()->json([
            'data' => new MobileUnitResource($unit),
        ]);
    }

    public function store(StoreUnitRequest $request): JsonResponse
    {
        $tenant = $this->tenant($request);
        abort_if(! $tenant, 404);
        $this->authorize('create', Unit::class);

        $data = $request->validated();
        $data['agent_id'] = $this->resolveAssignedAgents($request->input('agent_ids', []))->first() ?? null;
        $data = $this->applyPricingDefaults($data);
        $data['code'] = $this->generateCode($tenant, (int) $data['subcategory_id']);
        $data['photos'] = $this->storePhotos($request, 'units');

        if (! empty($data['location_url'])) {
            $coords = $this->parseLocationUrl((string) $data['location_url']);
            if ($coords) {
                $data['lat'] = $coords['lat'];
                $data['lng'] = $coords['lng'];
            }
        }

        $officialData = $this->validateOfficialData($request, true);
        $ownerData = $this->extractOwnerData($request);

        $unit = Unit::create($data);

        $unit->officialInfo()->updateOrCreate(
            ['unit_id' => $unit->id],
            array_merge($officialData['official'] ?? [], ['areas' => $officialData['areas'] ?? null])
        );

        if ($ownerData !== null) {
            $unit->owner()->updateOrCreate(['unit_id' => $unit->id], $ownerData);
        }

        $assignedAgents = $this->resolveAssignedAgents($request->input('agent_ids', []));
        if ($assignedAgents->isNotEmpty()) {
            $unit->syncAgents($assignedAgents->all());
        }

        $this->syncUnitAttributes($unit, $request->input('attributes'));

        $unit->load(['property', 'tenant', 'subcategory.category', 'city', 'officialInfo', 'owner', 'agents']);

        return response()->json([
            'message' => __('Unit created.'),
            'data' => new MobileUnitResource($unit),
        ], 201);
    }

    public function update(Request $request, Unit $unit): JsonResponse
    {
        $this->authorize('update', $unit);

        $data = validator($request->all(), $this->rulesForUpdate($unit))->validate();
        $assignedAgents = $this->resolveAssignedAgents($request->input('agent_ids', []));
        $data['agent_id'] = $assignedAgents->first() ?? $unit->agent_id;
        $data = $this->applyPricingDefaults($data, $unit);
        $data['photos'] = $this->storePhotos($request, 'units', is_array($unit->photos) ? $unit->photos : []);

        if (! empty($data['location_url'])) {
            $coords = $this->parseLocationUrl((string) $data['location_url']);
            if ($coords) {
                $data['lat'] = $coords['lat'];
                $data['lng'] = $coords['lng'];
            }
        }

        $unit->update($data);

        $officialData = $this->validateOfficialData($request, false);
        if (($officialData['official'] ?? []) !== [] || array_key_exists('areas', $officialData)) {
            $unit->officialInfo()->updateOrCreate(
                ['unit_id' => $unit->id],
                array_merge($officialData['official'] ?? [], ['areas' => $officialData['areas'] ?? null])
            );
        }

        $ownerData = $this->extractOwnerData($request);
        if ($ownerData !== null) {
            $unit->owner()->updateOrCreate(['unit_id' => $unit->id], $ownerData);
        }

        if ($assignedAgents->isNotEmpty()) {
            $unit->syncAgents($assignedAgents->all());
        } else {
            $unit->agents()->detach();
        }

        $this->syncUnitAttributes($unit, $request->input('attributes'));

        $unit->load(['property', 'tenant', 'subcategory.category', 'city', 'officialInfo', 'owner', 'agents']);

        return response()->json([
            'message' => __('Unit updated.'),
            'data' => new MobileUnitResource($unit),
        ]);
    }

    protected function tenant(Request $request): ?Tenant
    {
        return $this->tenants->tenant() ?: $request->attributes->get('mobile_tenant');
    }

    protected function resolveAssignedAgents(array $agentIds)
    {
        return Agent::query()->whereIn('id', $agentIds)->pluck('id');
    }

    protected function generateCode(Tenant $tenant, int $subcategoryId): string
    {
        $subcategory = Subcategory::find($subcategoryId);
        $categorySlug = $subcategory?->category?->slug ?? 'gen';
        $tenantPart = strtoupper(substr($tenant->slug, 0, 2));
        $catPart = strtoupper(substr($categorySlug, 0, 2));
        $nextSeq = Unit::where('tenant_id', $tenant->id)->where('subcategory_id', $subcategoryId)->count() + 1;

        return $tenantPart . $catPart . sprintf('%04d', $nextSeq);
    }

    protected function validateOfficialData(Request $request, bool $required): array
    {
        return validator($request->all(), [
            'official.directorate' => [$required ? 'required' : 'nullable', 'string', 'max:255'],
            'official.village' => [$required ? 'required' : 'nullable', 'string', 'max:255'],
            'official.basin_number' => [$required ? 'required' : 'nullable', 'string', 'max:50'],
            'official.basin_name' => [$required ? 'required' : 'nullable', 'string', 'max:255'],
            'official.plot_number' => [$required ? 'required' : 'nullable', 'string', 'max:50'],
            'official.apartment_number' => [$required ? 'required' : 'nullable', 'string', 'max:50'],
            'areas' => ['nullable', 'array'],
            'areas.land_sqm' => ['nullable', 'numeric', 'min:0'],
            'areas.built_sqm' => ['nullable', 'numeric', 'min:0'],
            'areas.total_sqm' => ['nullable', 'numeric', 'min:0'],
            'areas.notes' => ['nullable', 'string', 'max:500'],
        ])->validate();
    }

    protected function extractOwnerData(Request $request): ?array
    {
        $owner = (array) $request->input('owner', []);
        $filtered = array_filter($owner, fn ($value) => $value !== null && $value !== '');

        return $filtered === [] ? null : $filtered;
    }

    protected function syncUnitAttributes(Unit $unit, mixed $attributes): void
    {
        $unit->unitAttributes()->delete();

        if (! is_array($attributes) || $attributes === []) {
            return;
        }

        $fields = AttributeField::whereIn('id', array_keys($attributes))->get()->keyBy('id');

        foreach ($attributes as $fieldId => $valueData) {
            $field = $fields->get((int) $fieldId);
            if (! $field || ! is_array($valueData)) {
                continue;
            }

            $column = $this->attributeValueColumnForType($field->type);
            if (! array_key_exists($column, $valueData)) {
                continue;
            }

            $value = $valueData[$column];
            if ($value === '') {
                $value = null;
            }

            if ($field->type === 'bool') {
                $value = in_array($value, [1, '1', true, 'on'], true) ? 1 : 0;
            }

            if ($value !== null) {
                UnitAttribute::create([
                    'unit_id' => $unit->id,
                    'attribute_field_id' => (int) $fieldId,
                    $column => $value,
                ]);
            }
        }
    }

    protected function attributeValueColumnForType(string $type): string
    {
        return match ($type) {
            'int' => 'int_value',
            'decimal' => 'decimal_value',
            'string' => 'string_value',
            'bool' => 'bool_value',
            default => 'json_value',
        };
    }

    protected function rulesForUpdate(Unit $unit): array
    {
        return [
            'property_id' => ['nullable', 'exists:properties,id'],
            'subcategory_id' => ['required', 'integer', 'exists:subcategories,id'],
            'title.en' => ['required', 'string', 'max:500'],
            'title.ar' => ['nullable', 'string', 'max:500'],
            'description.en' => ['nullable', 'string', 'max:2000'],
            'description.ar' => ['nullable', 'string', 'max:2000'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'area_id' => ['nullable', 'integer', 'exists:states,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'currency' => ['required', 'string', 'size:3', 'in:USD,JOD,IQD'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['required', 'string', 'in:' . implode(',', Unit::STATUSES)],
            'listing_type' => ['required', 'string', 'in:' . implode(',', Unit::LISTING_TYPES)],
            'photos' => ['nullable', 'array', 'max:50'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'location' => ['nullable', 'string', 'max:500'],
            'location_url' => ['nullable', 'string', 'max:2000'],
            'attributes' => ['nullable', 'array'],
            'agent_ids' => ['nullable', 'array'],
            'agent_ids.*' => ['integer', 'exists:agents,id'],
        ];
    }

    protected function applyPricingDefaults(array $data, ?Unit $unit = null): array
    {
        if (! array_key_exists('price', $data) || $data['price'] === null) {
            $data['price'] = $unit?->price ?? 0;
        }
        if (! array_key_exists('currency', $data) || $data['currency'] === null) {
            $data['currency'] = $unit?->currency ?? 'IQD';
        }

        return $data;
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

    protected function applyUnitSearch(Builder $query, string $term): Builder
    {
        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($term) {
            $inner->where('title', 'like', "%$term%")
                ->orWhereHas('property', function (Builder $property) use ($term) {
                    $property->where('name', 'like', "%$term%")
                        ->orWhere('address', 'like', "%$term%");
                });
        });
    }

    protected function applyUnitSubcategoryFilter(Builder $query, int $subcategoryId): Builder
    {
        return $subcategoryId > 0 ? $query->where('subcategory_id', $subcategoryId) : $query;
    }

    protected function applyUnitStatusFilter(Builder $query, string $status): Builder
    {
        return in_array($status, Unit::STATUSES, true) ? $query->where('status', $status) : $query;
    }

    protected function applyUnitPropertyFilter(Builder $query, int $propertyId): Builder
    {
        return $propertyId > 0 ? $query->where('property_id', $propertyId) : $query;
    }

    protected function applyUnitAgentFilter(Builder $query, int $agentId): Builder
    {
        if ($agentId <= 0) {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($agentId) {
            $inner->where('agent_id', $agentId)
                ->orWhereHas('agents', fn (Builder $agents) => $agents->where('agents.id', $agentId));
        });
    }

    protected function applyUnitPriceFilter(Builder $query, float $minPrice, float $maxPrice): Builder
    {
        if ($minPrice > 0) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice > 0) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    protected function applyUnitBedsFilter(Builder $query, int $beds): Builder
    {
        return $beds > 0 ? $query->where('beds', '>=', $beds) : $query;
    }

    protected function applyUnitBathsFilter(Builder $query, float $baths): Builder
    {
        return $baths > 0 ? $query->where('baths', '>=', $baths) : $query;
    }

    protected function applyUnitListingTypeFilter(Builder $query, string $listingType): Builder
    {
        return in_array($listingType, Unit::LISTING_TYPES, true) ? $query->where('listing_type', $listingType) : $query;
    }
}
