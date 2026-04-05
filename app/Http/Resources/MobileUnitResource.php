<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileUnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $displayPrice = ($this->listing_type ?? 'rent') === 'sale'
            ? $this->price
            : (($this->market_rent && $this->market_rent > 0) ? $this->market_rent : $this->price);

        $locationLabel = collect([
            $this->location,
            $this->city?->{app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'},
            $this->city?->name_en,
            $this->area?->{app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'},
            $this->area?->name_en,
            $this->property?->city,
            $this->property?->address,
        ])->map(fn ($value) => trim((string) $value))
            ->first(fn ($value) => $value !== '');

        return [
            'id' => $this->id,
            'code' => $this->code,
            'tenant_id' => $this->tenant_id,
            'property_id' => $this->property_id,
            'subcategory_id' => $this->subcategory_id,
            'title' => $this->title,
            'translated_title' => $this->translated_title,
            'description' => $this->description,
            'translated_description' => $this->translated_description,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'price' => $this->price,
            'currency' => $this->currency,
            'market_rent' => $this->market_rent,
            'display_price' => $displayPrice,
            'display_price_period' => ($this->listing_type ?? 'rent') === 'rent' ? 'year' : null,
            'status' => $this->status,
            'listing_type' => $this->listing_type,
            'beds' => $this->beds,
            'baths' => $this->baths,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'sqft' => $this->sqft,
            'area_m2' => $this->area_m2,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'location' => $this->location,
            'location_label' => $locationLabel,
            'location_url' => $this->location_url,
            'photos' => $this->photos ?? [],
            'property' => $this->whenLoaded('property', function () {
                return [
                    'id' => $this->property?->id,
                    'name' => $this->property?->name,
                    'address' => $this->property?->address,
                    'city' => $this->property?->city,
                    'country' => $this->property?->country,
                ];
            }),
            'tenant' => $this->whenLoaded('tenant', fn () => new MobileTenantResource($this->tenant)),
            'city' => $this->whenLoaded('city', function () {
                return [
                    'id' => $this->city?->id,
                    'name_en' => $this->city?->name_en,
                    'name_ar' => $this->city?->name_ar,
                ];
            }),
            'area' => $this->whenLoaded('area', function () {
                return [
                    'id' => $this->area?->id,
                    'name_en' => $this->area?->name_en,
                    'name_ar' => $this->area?->name_ar,
                ];
            }),
            'subcategory' => $this->whenLoaded('subcategory', function () {
                return [
                    'id' => $this->subcategory?->id,
                    'name' => $this->subcategory?->name,
                    'category_id' => $this->subcategory?->category_id,
                    'category_name' => $this->subcategory?->category?->name,
                ];
            }),
            'agent_ids' => $this->relationLoaded('agents') ? $this->agents->pluck('id')->values()->all() : [],
            'official' => $this->whenLoaded('officialInfo', function () {
                return [
                    'directorate' => $this->officialInfo?->directorate,
                    'village' => $this->officialInfo?->village,
                    'basin_number' => $this->officialInfo?->basin_number,
                    'basin_name' => $this->officialInfo?->basin_name,
                    'plot_number' => $this->officialInfo?->plot_number,
                    'apartment_number' => $this->officialInfo?->apartment_number,
                    'areas' => $this->officialInfo?->areas,
                ];
            }),
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'name' => $this->owner?->name,
                    'phone' => $this->owner?->phone,
                    'email' => $this->owner?->email,
                    'notes' => $this->owner?->notes,
                ];
            }),
            'attributes' => $this->whenLoaded('unitAttributes', function () {
                return $this->unitAttributes->map(function ($attribute) {
                    $field = $attribute->attributeField;

                    return [
                        'attribute_field_id' => $attribute->attribute_field_id,
                        'key' => $field?->key,
                        'label' => $field?->translated_label,
                        'group' => $field?->group,
                        'searchable' => (bool) $field?->searchable,
                        'promoted' => (bool) $field?->promoted,
                        'formatted_value' => $attribute->formatted_value,
                        'int_value' => $attribute->int_value,
                        'decimal_value' => $attribute->decimal_value,
                        'string_value' => $attribute->string_value,
                        'bool_value' => $attribute->bool_value,
                        'json_value' => $attribute->json_value,
                    ];
                })->values()->all();
            }),
            'attribute_highlights' => $this->whenLoaded('unitAttributes', function () {
                return $this->unitAttributes
                    ->map(function ($attribute) {
                        $field = $attribute->attributeField;
                        $formatted = $attribute->formatted_value;

                        if (! $field || $formatted === null || trim((string) $formatted) === '') {
                            return null;
                        }

                        $group = strtolower((string) $field->group);
                        $featured = (bool) $field->promoted
                            || (bool) $field->searchable
                            || str_contains($group, 'life')
                            || str_contains($group, 'amen')
                            || str_contains($group, 'view')
                            || str_contains($group, 'finish')
                            || str_contains($group, 'feature')
                            || str_contains($group, 'community');

                        return [
                            'label' => $field->translated_label,
                            'value' => $formatted,
                            'group' => $field->group,
                            'featured' => $featured,
                        ];
                    })
                    ->filter()
                    ->sortByDesc(fn ($item) => $item['featured'])
                    ->take(4)
                    ->values()
                    ->all();
            }),
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}
