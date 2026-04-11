<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResidentListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentMethodRules = ['nullable', 'string', 'max:64'];

        return [
            'title.en' => ['required', 'string', 'max:255'],
            'title.ar' => ['nullable', 'string', 'max:255'],
            'description.en' => ['required', 'string', 'max:2000'],
            'description.ar' => ['nullable', 'string', 'max:2000'],
            'subcategory_id' => ['required', 'exists:subcategories,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'area_id' => ['nullable', 'exists:states,id'],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:20'],
            'bathrooms' => ['required', 'numeric', 'min:0', 'max:20'],
            'area_m2' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'location' => ['nullable', 'string', 'max:500'],
            'location_url' => ['nullable', 'string', 'max:1000'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['nullable'],
            'listing_type' => ['required', 'in:rent,sale'],
            'ad_duration_id' => ['required', 'exists:ad_durations,id'],
            'payment_method' => $paymentMethodRules,
            'payment_reference' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.en.required' => 'Please provide a title in English.',
            'description.en.required' => 'Please provide a description in English.',
            'subcategory_id.required' => 'Please select a property type.',
            'city_id.required' => 'Please select a city.',
            'bedrooms.required' => 'Please specify the number of bedrooms.',
            'bathrooms.required' => 'Please specify the number of bathrooms.',
            'price.required' => 'Please enter the price.',
            'listing_type.required' => 'Please select whether this is for rent or sale.',
            'ad_duration_id.required' => 'Please select an ad duration.',
        ];
    }
}
