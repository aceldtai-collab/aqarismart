<?php

namespace App\Http\Controllers;

use App\Models\AttributeField;
use App\Models\Subcategory;
use App\Services\Tenancy\TenantManager;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class TenantAttributeFieldController extends Controller
{
    protected array $attributeTypes = ['bool', 'int', 'decimal', 'string', 'enum', 'multi_enum', 'date', 'json'];

    protected array $attributeGroups = [
        'Basics',
        'Building',
        'Comfort',
        'Condition',
        'Costs',
        'Extras',
        'Features',
        'Infrastructure',
        'Interiors',
        'Land',
        'Layout',
        'Location',
        'Luxury',
        'Operations',
        'Outdoor',
        'Paperwork',
        'Parking',
        'Safety',
        'Security',
        'Services',
        'Structure',
        'Use Case',
        'Utilities',
        'Visibility',
    ];

    public function __construct(protected TenantManager $tenants) {}

    public function index(Request $request): View|RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $filterKeys = ['q', 'subcategory_id', 'type', 'group'];
        $sessionKey = 'custom_attributes.filters';

        if ($request->boolean('clear')) {
            $request->session()->forget($sessionKey);

            return redirect()->route('custom-attributes.index', $request->only('lang'));
        }

        $rememberedFilters = $request->session()->get($sessionKey, []);
        if (! $request->hasAny($filterKeys) && ! empty($rememberedFilters)) {
            return redirect()->route('custom-attributes.index', $request->only('lang') + $rememberedFilters);
        }

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'subcategory_id' => (int) $request->query('subcategory_id', 0),
            'type' => trim((string) $request->query('type', '')),
            'group' => trim((string) $request->query('group', '')),
        ];

        $activeFilters = array_filter($filters, fn ($value) => filled($value) && $value !== 0);
        if ($request->hasAny($filterKeys)) {
            if (empty($activeFilters)) {
                $request->session()->forget($sessionKey);
            } else {
                $request->session()->put($sessionKey, $activeFilters);
            }
        }

        $query = AttributeField::with('subcategory')
            ->forTenant($tenant->id)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $term = $filters['q'];

                $query->where(function ($inner) use ($term) {
                    $inner->where('label', 'like', "%{$term}%")
                        ->orWhere('key', 'like', "%{$term}%")
                        ->orWhere('unit', 'like', "%{$term}%");
                });
            })
            ->when($filters['subcategory_id'] > 0, fn ($query) => $query->where('subcategory_id', $filters['subcategory_id']))
            ->when($filters['type'] !== '', fn ($query) => $query->where('type', $filters['type']))
            ->when($filters['group'] !== '', fn ($query) => $query->where('group', $filters['group']))
            ->orderBy('subcategory_id')
            ->orderBy('group')
            ->orderBy('sort');

        $fields = $query->get();

        $globalCount = AttributeField::global()->count();
        $subcategories = Subcategory::orderBy('name')->get();
        $types = $this->attributeTypes;
        $groups = $this->attributeGroups;

        return view('custom-attributes.index', compact('fields', 'tenant', 'globalCount', 'subcategories', 'types', 'groups', 'filters', 'activeFilters'));
    }

    public function create(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $subcategories = Subcategory::with('category')->orderBy('category_id')->orderBy('name')->get();
        $types = $this->attributeTypes;
        $groups = $this->attributeGroups;

        return view('custom-attributes.create', compact('subcategories', 'types', 'groups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $data = $request->validate([
            'subcategory_id' => ['required', 'exists:subcategories,id'],
            'label_en' => ['required', 'string', 'max:255'],
            'label_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:bool,int,decimal,string,enum,multi_enum,date,json'],
            'group' => ['required', 'string', 'max:255'],
            'options' => ['nullable', 'array'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        $key = \Illuminate\Support\Str::slug($data['label_en'], '_') . '_' . $tenant->id;

        $existing = AttributeField::query()
            ->where('tenant_id', $tenant->id)
            ->where('subcategory_id', $data['subcategory_id'])
            ->where('key', $key)
            ->exists();

        if ($existing) {
            return redirect()->back()->withInput()->withErrors([
                'label_en' => __('An attribute with this name already exists for the selected category.'),
            ]);
        }

        try {
            AttributeField::create([
                'tenant_id' => $tenant->id,
                'subcategory_id' => $data['subcategory_id'],
                'key' => $key,
                'label' => $data['label_en'],
                'label_translations' => $this->normalizedTranslations($data['label_en'], $data['label_ar'] ?? null),
                'type' => $data['type'],
                'required' => false,
                'searchable' => false,
                'facetable' => false,
                'promoted' => false,
                'group' => $data['group'],
                'sort' => 500,
                'options' => $this->normalizedOptions($data['options'] ?? null, $data['type']),
                'unit' => $data['unit'] ?? null,
            ]);
        } catch (UniqueConstraintViolationException) {
            return redirect()->back()->withInput()->withErrors([
                'label_en' => __('An attribute with this name already exists for the selected category.'),
            ]);
        }

        return redirect()->route('custom-attributes.index')->with('status', __('Attribute created successfully'));
    }

    public function edit(string $tenant_slug, string $customAttribute): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $customAttribute = $this->resolveCustomAttribute($customAttribute);
        abort_if($customAttribute->tenant_id !== $tenant->id, 403);

        $subcategories = Subcategory::orderBy('name')->get();
        $types = $this->attributeTypes;
        $groups = $this->attributeGroups;

        return view('custom-attributes.edit', compact('customAttribute', 'subcategories', 'types', 'groups'));
    }

    public function update(Request $request, string $tenant_slug, string $customAttribute): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $customAttribute = $this->resolveCustomAttribute($customAttribute);
        abort_if($customAttribute->tenant_id !== $tenant->id, 403);

        $data = $request->validate([
            'subcategory_id' => ['required', 'exists:subcategories,id'],
            'label_en' => ['required', 'string', 'max:255'],
            'label_ar' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:bool,int,decimal,string,enum,multi_enum,date,json'],
            'group' => ['required', 'string', 'max:255'],
            'options' => ['nullable', 'array'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        $customAttribute->update([
            'subcategory_id' => $data['subcategory_id'],
            'label' => $data['label_en'],
            'label_translations' => $this->normalizedTranslations($data['label_en'], $data['label_ar'] ?? null),
            'type' => $data['type'],
            'group' => $data['group'],
            'options' => $this->normalizedOptions($data['options'] ?? null, $data['type']),
            'unit' => $data['unit'] ?? null,
        ]);

        return redirect()->route('custom-attributes.index')->with('status', __('Attribute updated successfully'));
    }

    public function destroy(string $tenant_slug, string $customAttribute): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $customAttribute = $this->resolveCustomAttribute($customAttribute);
        abort_if($customAttribute->tenant_id !== $tenant->id, 403);

        try {
            $customAttribute->delete();
        } catch (QueryException $exception) {
            return redirect()
                ->route('custom-attributes.index')
                ->with('error', __('This attribute is still in use by unit records and cannot be deleted yet.'));
        }

        return redirect()->route('custom-attributes.index')->with('status', __('Attribute deleted successfully'));
    }

    public function updateSort(Request $request): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $data = $request->validate([
            'sorts' => ['required', 'array'],
            'sorts.*' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        foreach ($data['sorts'] as $fieldId => $sortValue) {
            AttributeField::query()
                ->where('id', $fieldId)
                ->where('tenant_id', $tenant->id)
                ->update(['sort' => $sortValue]);
        }

        return redirect()->route('custom-attributes.index')->with('status', __('Sort order updated successfully'));
    }

    protected function resolveCustomAttribute(string $customAttribute): AttributeField
    {
        return AttributeField::query()->findOrFail($customAttribute);
    }

    protected function normalizedTranslations(string $labelEn, ?string $labelAr): array
    {
        return array_filter([
            'en' => $labelEn,
            'ar' => $labelAr,
        ], fn ($value) => filled($value));
    }

    protected function normalizedOptions(?array $options, string $type): ?array
    {
        if (! in_array($type, ['enum', 'multi_enum'], true)) {
            return null;
        }

        $normalized = collect($options ?? [])
            ->mapWithKeys(function ($value, $key) {
                $optionKey = trim((string) $key);
                $optionValue = is_array($value)
                    ? trim((string) Arr::first($value))
                    : trim((string) $value);

                if ($optionKey === '' || $optionValue === '') {
                    return [];
                }

                return [$optionKey => $optionValue];
            })
            ->all();

        return $normalized !== [] ? $normalized : null;
    }
}
