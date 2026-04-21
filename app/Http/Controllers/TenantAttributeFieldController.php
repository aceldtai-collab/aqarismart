<?php

namespace App\Http\Controllers;

use App\Models\AttributeField;
use App\Models\Subcategory;
use App\Services\Tenancy\TenantManager;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class TenantAttributeFieldController extends Controller
{
    public function __construct(protected TenantManager $tenants) {}

    public function index(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $fields = AttributeField::with('subcategory')
            ->forTenant($tenant->id)
            ->orderBy('subcategory_id')
            ->orderBy('group')
            ->orderBy('sort')
            ->get();

        $globalCount = AttributeField::global()->count();

        return view('custom-attributes.index', compact('fields', 'tenant', 'globalCount'));
    }

    public function create(): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);

        $subcategories = Subcategory::orderBy('name')->get();
        $types = ['bool', 'int', 'decimal', 'string', 'enum', 'multi_enum', 'date', 'json'];
        $groups = ['Basics', 'Comfort', 'Building', 'Utilities', 'Extras', 'Luxury', 'Outdoor', 'Features'];

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

        return redirect()->route('custom-attributes.index')->with('status', __('Attribute created successfully'));
    }

    public function edit(string $customAttribute): View
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $customAttribute = $this->resolveCustomAttribute($customAttribute, $tenant->id);
        abort_if($customAttribute->tenant_id !== $tenant->id, 403);

        $subcategories = Subcategory::orderBy('name')->get();
        $types = ['bool', 'int', 'decimal', 'string', 'enum', 'multi_enum', 'date', 'json'];
        $groups = ['Basics', 'Comfort', 'Building', 'Utilities', 'Extras', 'Luxury', 'Outdoor', 'Features'];

        return view('custom-attributes.edit', compact('customAttribute', 'subcategories', 'types', 'groups'));
    }

    public function update(Request $request, string $customAttribute): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $customAttribute = $this->resolveCustomAttribute($customAttribute, $tenant->id);
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

    public function destroy(string $customAttribute): RedirectResponse
    {
        $tenant = $this->tenants->tenant();
        abort_if(! $tenant, 404);
        $customAttribute = $this->resolveCustomAttribute($customAttribute, $tenant->id);
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

    protected function resolveCustomAttribute(string $customAttribute, int $tenantId): AttributeField
    {
        return AttributeField::query()
            ->forTenant($tenantId)
            ->findOrFail($customAttribute);
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
