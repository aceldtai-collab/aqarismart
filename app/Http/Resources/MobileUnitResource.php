<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileUnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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
                    return [
                        'attribute_field_id' => $attribute->attribute_field_id,
                        'int_value' => $attribute->int_value,
                        'decimal_value' => $attribute->decimal_value,
                        'string_value' => $attribute->string_value,
                        'bool_value' => $attribute->bool_value,
                        'json_value' => $attribute->json_value,
                    ];
                })->values()->all();
            }),
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}
