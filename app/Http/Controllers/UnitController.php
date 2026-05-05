<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitRequest;
use App\Http\Controllers\Concerns\ResolvesAgentAssignments;
use App\Http\Controllers\Concerns\StoresPhotos;
use App\Models\AttributeField;
use App\Models\Category;
use App\Models\Property;
use App\Models\Subcategory;
use App\Models\Unit;
use App\Models\UnitAttribute;
use App\Services\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class UnitController extends Controller
{
    use ResolvesAgentAssignments;
    use StoresPhotos;

    public function __construct(protected TenantManager $tenants) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('viewAny', Unit::class);

        $q = trim((string) request()->query('q', ''));
        $subcategory_id = (int) request()->query('subcategory_id', 0);
        $property_id = (int) request()->query('property_id', 0);
        $status = trim((string) request()->query('status', ''));
        $listing_type = trim((string) request()->query('listing_type', ''));
        $agent_id = (int) request()->query('agent_id', 0);
        $min_price = (float) request()->query('min_price', 0);
        $max_price = (float) request()->query('max_price', 0);
        $beds = (int) request()->query('beds', 0);
        $baths = (float) request()->query('baths', 0);

        $query = Unit::with(['property.agent', 'property.agents', 'agent', 'agents', 'subcategory.category']);
        $query = $this->applyUnitSearch($query, $q);
        $query = $this->applyUnitSubcategoryFilter($query, $subcategory_id);
        $query = $this->applyUnitPropertyFilter($query, $property_id);
        $query = $this->applyUnitStatusFilter($query, $status);
        $query = $this->applyUnitListingTypeFilter($query, $listing_type);
        $query = $this->applyUnitAgentFilter($query, $agent_id);
        $query = $this->applyUnitPriceFilter($query, $min_price, $max_price);
        $query = $this->applyUnitBedsFilter($query, $beds);
        $query = $this->applyUnitBathsFilter($query, $baths);
        
        if ($agentId = auth()->user()?->agent_id) {
            $query->forAgent($agentId);
        }
        $query = $query->orderByDesc('id');

        $units = $query->paginate(15);
        $subcategories = \App\Models\Subcategory::orderBy('name')->get();
        $properties = $this->availableProperties();
        $agents = $this->availableAgents();
        
        return view('units.index', compact('units', 'subcategories', 'properties', 'agents') + [
            'q' => $q,
            'subcategory_id' => $subcategory_id,
            'property_id' => $property_id,
            'status' => $status,
            'listing_type' => $listing_type,
            'agent_id' => $agent_id,
            'min_price' => $min_price,
            'max_price' => $max_price,
            'beds' => $beds,
            'baths' => $baths,
        ]);
    }

    public function create(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', Unit::class);

        return view('units.create', $this->unitFormData());
    }

    public function store(StoreUnitRequest $request): RedirectResponse
    {
        // dd($request);
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('create', Unit::class);

        $data = $request->validated();
        $assignedAgents = $this->resolveAssignedAgents($request->input('agent_ids', []));
        $data['agent_id'] = $assignedAgents->first() ?? null;
        $data = $this->applyPricingDefaults($data);

        // Auto-generate code: 8 chars (Tenant slug 2 + Category slug 2 + 4-digit seq)
        $subcategory = Subcategory::find($data['subcategory_id']);
        $categorySlug = $subcategory?->category?->slug ?? 'gen';
        $tenantSlug = $tenant->slug;

        $tenantPart = strtoupper(substr($tenantSlug, 0, 2));
        $catPart = strtoupper(substr($categorySlug, 0, 2));

        $nextSeq = Unit::where('tenant_id', $tenant->id)
            ->where('subcategory_id', $data['subcategory_id'])
            ->count() + 1;
        $seqPart = sprintf('%04d', $nextSeq);

        $data['code'] = $tenantPart . $catPart . $seqPart;

        $data['photos'] = $this->storePhotos($request, 'units');

        // Extract lat/lng from location_url
        if (!empty($data['location_url'])) {
            $coords = $this->parseLocationUrl($data['location_url']);
            if ($coords) {
                $data['lat'] = $coords['lat'];
                $data['lng'] = $coords['lng'];
            }
        }

        // officaialData
        $officialData = $request->validate([
            'official.directorate' => ['required', 'string', 'max:255'],
            'official.village' => ['required', 'string', 'max:255'],
            'official.basin_number' => ['required', 'string', 'max:50'],
            'official.basin_name' => ['required', 'string', 'max:255'],
            'official.plot_number' => ['required', 'string', 'max:50'],
            'official.apartment_number' => ['required', 'string', 'max:50'],
            'areas' => ['nullable', 'array'],
            'areas.land_sqm' => ['nullable', 'numeric', 'min:0'],
            'areas.built_sqm' => ['nullable', 'numeric', 'min:0'],
            'areas.total_sqm' => ['nullable', 'numeric', 'min:0'],
            'areas.notes' => ['nullable', 'string', 'max:500'],
        ]);
        $unit = Unit::create($data);

        $unit->officialInfo()->updateOrCreate(
            ['unit_id' => $unit->id],
            array_merge($officialData['official'] ?? [], ['areas' => $officialData['areas'] ?? null])
        );

        // Owner data
        if ($request->filled('owner.name') || $request->filled('owner.phone') || $request->filled('owner.email')) {
            $unit->owner()->updateOrCreate(
                ['unit_id' => $unit->id],
                $request->only(['owner.name', 'owner.phone', 'owner.email', 'owner.notes'])['owner'] ?? []
            );
        }

        if ($assignedAgents->isNotEmpty()) {
            $unit->syncAgents($assignedAgents->all());
        } else {
            $unit->agents()->detach();
        }

        $this->processUnitAttributes($unit, $request);

        return redirect()->route('units.index')->with('status', 'Unit created.');
    }

    public function show(Unit $unit): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('view', $unit);

        $unit->load(['property.agent', 'property.agents', 'agent', 'agents', 'subcategory.category', 'city', 'area', 'unitAttributes.attributeField']);

        $attributeFields = AttributeField::where('subcategory_id', $unit->subcategory_id)->orderBy('sort')->get();
        $unitAttributes = $unit->unitAttributes->keyBy('attribute_field_id');

        return view('units.show', compact('unit', 'attributeFields', 'unitAttributes'));
    }

    public function edit(?String $tenant, Unit $unit): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('update', $unit);

        $unit->load(['unitAttributes', 'officialInfo', 'owner']);

        $unitAttributes = $unit->unitAttributes->values();

        return view('units.edit', array_merge(
            $this->unitFormData(),
            compact('unit', 'unitAttributes')
        ));
    }

    public function update(StoreUnitRequest $request, ?String $tenant, Unit $unit): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('update', $unit);

        $data = $request->validated();
        $assignedAgents = $this->resolveAssignedAgents($request->input('agent_ids', []));
        $data['agent_id'] = $assignedAgents->first() ?? $unit->agent_id;

        $data = $this->applyPricingDefaults($data, $unit);

        $data['photos'] = $this->storePhotos($request, 'units', $unit->photos ?? []);

        // Extract lat/lng from location_url
        if (!empty($data['location_url'])) {
            $coords = $this->parseLocationUrl($data['location_url']);
            if ($coords) {
                $data['lat'] = $coords['lat'];
                $data['lng'] = $coords['lng'];
            }
        }

        $unit->update($data);

        // Official data
        if ($request->has('official')) {
            $officialData = $request->validate([
                'official.directorate' => ['nullable', 'string', 'max:255'],
                'official.village' => ['nullable', 'string', 'max:255'],
                'official.basin_number' => ['nullable', 'string', 'max:50'],
                'official.basin_name' => ['nullable', 'string', 'max:255'],
                'official.plot_number' => ['nullable', 'string', 'max:50'],
                'official.apartment_number' => ['nullable', 'string', 'max:50'],
                'official.areas' => ['nullable', 'array'],
                'official.areas.land_sqm' => ['nullable', 'numeric', 'min:0'],
                'official.areas.built_sqm' => ['nullable', 'numeric', 'min:0'],
                'official.areas.total_sqm' => ['nullable', 'numeric', 'min:0'],
                'official.areas.notes' => ['nullable', 'string', 'max:500'],
            ]);
            $unit->officialInfo()->updateOrCreate(
                ['unit_id' => $unit->id],
                $officialData['official']
            );
        }

        // Owner data
        if ($request->filled('owner.name') || $request->filled('owner.phone') || $request->filled('owner.email')) {
            $unit->owner()->updateOrCreate(
                ['unit_id' => $unit->id],
                $request->only(['owner.name', 'owner.phone', 'owner.email', 'owner.notes'])['owner'] ?? []
            );
        }

        if ($assignedAgents->isNotEmpty()) {
            $unit->syncAgents($assignedAgents->all());
        } else {
            $unit->agents()->detach();
        }

        $this->processUnitAttributes($unit, $request);

        return redirect()->route('units.index')->with('status', 'Unit updated.');
    }

    public function destroy(?String $tenant, Unit $unit): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $this->authorize('delete', $unit);
        $unit->delete();
        return redirect()->route('units.index')->with('status', 'Unit removed.');
    }

    public function tenantShow(string $tenant_slug, string $unit): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $unit = Unit::forTenant($tenant)
            ->where('code', $unit)
            ->firstOrFail();

        $unit->load([
            'property',
            'property.agents',
            'property.agent',
            'unitAttributes.attributeField',
            'subcategory',
            'city',
            'area',
            'agents',
            'agent',
        ]);

        $attributeFields = AttributeField::where('subcategory_id', $unit->subcategory_id)->orderBy('sort')->get();

        return view('tenant.unit', [
            'unit' => $unit,
            'attributeFields' => $attributeFields,
            'tenantCtx' => $tenant,
        ]);
    }

    private function processUnitAttributes(Unit $unit, Request $request): void
    {
        $unit->unitAttributes()->delete();

        $attributes = $request->input('attributes');
        if (! is_array($attributes) || $attributes === []) {
            return;
        }

        $fields = AttributeField::whereIn('id', array_keys($attributes))
            ->get()
            ->keyBy('id');

        foreach ($attributes as $fieldId => $valueData) {
            $field = $fields->get((int) $fieldId);
            if (!$field) {
                continue;
            }
            if (! is_array($valueData)) {
                continue;
            }

            $type = $field->type;
            $column = $this->attributeValueColumnForType($type);

            if (!isset($valueData[$column])) {
                continue;
            }

            $value = $valueData[$column];
            if ($value === '') {
                $value = null;
            }

            if ($type === 'bool') {
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

    private function attributeValueColumnForType(string $type): string
    {
        return match ($type) {
            'int' => 'int_value',
            'decimal' => 'decimal_value',
            'string' => 'string_value',
            'bool' => 'bool_value',
            default => 'json_value',
        };
    }

    protected function availableProperties(): Collection
    {
        $properties = Property::orderBy('name');
        if ($cid = auth()->user()?->agent_id) {
            $properties->where('agent_id', $cid);
        }

        return $properties->get();
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
        if ($subcategoryId <= 0) {
            return $query;
        }

        return $query->where('subcategory_id', $subcategoryId);
    }

    protected function applyUnitStatusFilter(Builder $query, string $status): Builder
    {
        if ($status === Unit::STATUS_VACANT) {
            return $query->where('status', Unit::STATUS_VACANT);
        }
        if ($status === Unit::STATUS_OCCUPIED) {
            return $query->where('status', Unit::STATUS_OCCUPIED);
        }

        return $query;
    }

    protected function applyUnitPropertyFilter(Builder $query, int $propertyId): Builder
    {
        if ($propertyId <= 0) {
            return $query;
        }
        return $query->where('property_id', $propertyId);
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
        if ($beds <= 0) {
            return $query;
        }
        return $query->where('beds', '>=', $beds);
    }

    protected function applyUnitBathsFilter(Builder $query, float $baths): Builder
    {
        if ($baths <= 0) {
            return $query;
        }
        return $query->where('baths', '>=', $baths);
    }

    protected function applyUnitListingTypeFilter(Builder $query, string $listingType): Builder
    {
        if (in_array($listingType, Unit::LISTING_TYPES, true)) {
            return $query->where('listing_type', $listingType);
        }

        return $query;
    }

    // storePhotos provided by StoresPhotos.
    protected function unitFormData(): array
    {
        $properties = $this->availableProperties();
        $categories = Category::with('subcategories')->orderBy('name')->get();
        $propMeta = $properties->map(fn($p) => ['id' => $p->id, 'category_id' => $p->category_id])->values();
        $catMeta = $categories->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'subs' => $c->subcategories->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values(),
            ];
        })->values();
        $tenant = $this->tenants->tenant();
        $attributeFields = $tenant
            ? AttributeField::with('subcategory')->availableTo($tenant->id)->orderBy('sort')->get()
            : AttributeField::with('subcategory')->global()->orderBy('sort')->get();
        $agents = $this->availableAgents();

        return compact('properties', 'categories', 'propMeta', 'catMeta', 'attributeFields', 'agents');
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

    public function mobileIndex(Request $request): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $units = Unit::where('tenant_id', $tenant->id)
            ->with(['property', 'category', 'subcategory', 'tenant'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('mobile.units.index', compact('tenant', 'units'));
    }

    public function mobileShow(Unit $unit): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        abort_if($unit->tenant_id !== $tenant->id, 404);

        $unit->load(['property', 'category', 'subcategory', 'tenant', 'agent', 'photos']);

        return view('mobile.units.show', compact('tenant', 'unit'));
    }
}
