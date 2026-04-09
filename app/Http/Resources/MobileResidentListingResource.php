<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MobileResidentListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => [
                'en' => $this->title['en'] ?? '',
                'ar' => $this->title['ar'] ?? '',
            ],
            'description' => [
                'en' => $this->description['en'] ?? '',
                'ar' => $this->description['ar'] ?? '',
            ],
            'subcategory' => $this->whenLoaded('subcategory', function () {
                return [
                    'id' => $this->subcategory->id,
                    'name' => $this->subcategory->name,
                    'category' => $this->whenLoaded('subcategory.category', function () {
                        return [
                            'id' => $this->subcategory->category->id,
                            'name' => $this->subcategory->category->name,
                        ];
                    }),
                ];
            }),
            'city' => $this->whenLoaded('city', function () {
                return [
                    'id' => $this->city->id,
                    'name_en' => $this->city->name_en,
                    'name_ar' => $this->city->name_ar,
                ];
            }),
            'area' => $this->whenLoaded('area', function () {
                return $this->area ? [
                    'id' => $this->area->id,
                    'name_en' => $this->area->name_en,
                    'name_ar' => $this->area->name_ar,
                ] : null;
            }),
            'bedrooms' => $this->bedrooms,
            'bathrooms' => (float) $this->bathrooms,
            'area_m2' => (float) $this->area_m2,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            'location' => $this->location,
            'location_url' => $this->location_url,
            'coordinates' => [
                'lat' => $this->lat ? (float) $this->lat : null,
                'lng' => $this->lng ? (float) $this->lng : null,
            ],
            'photos' => $this->photos ?? [],
            'first_photo' => $this->first_photo,
            'listing_type' => $this->listing_type,
            'source' => $this->source,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'phone' => $this->user->phone,
                    'phone_country_code' => $this->user->phone_country_code,
                ];
            }),
            'ad_duration' => $this->whenLoaded('adDuration', function () {
                return $this->adDuration ? [
                    'id' => $this->adDuration->id,
                    'name_en' => $this->adDuration->name_en,
                    'name_ar' => $this->adDuration->name_ar,
                    'days' => $this->adDuration->days,
                ] : null;
            }),
            'ad_started_at' => $this->ad_started_at?->toIso8601String(),
            'ad_expires_at' => $this->ad_expires_at?->toIso8601String(),
            'ad_status' => $this->ad_status,
            'days_until_expiration' => $this->days_until_expiration,
            'is_expired' => $this->is_expired,
            'is_expiring_soon' => $this->is_expiring_soon,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'amount_paid' => $this->amount_paid ? (float) $this->amount_paid : null,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
