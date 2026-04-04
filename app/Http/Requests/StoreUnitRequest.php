<?php

namespace App\Http\Requests;

use App\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty string to null for property_id
        if ($this->property_id === '' || $this->property_id === 'all') {
            $this->merge(['property_id' => null]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Unit::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'property_id' => ['nullable', 'exclude_if:property_id,', 'exists:properties,id'],
            'subcategory_id' => ['required','integer','exists:subcategories,id'],
            'title.en' => ['required','string','max:500'],
            'title.ar' => ['nullable','string','max:500'],
            'description.en' => ['nullable','string','max:2000'],
            'description.ar' => ['nullable','string','max:2000'],
            'city_id' => ['nullable','integer','exists:cities,id'],
            'area_id' => ['nullable','integer','exists:states,id'],
            'price' => ['required','numeric','min:0','max:999999'],
            'currency' => ['required','string','size:3','in:USD,JOD'],
            'lat' => ['nullable','numeric','between:-90,90'],
            'lng' => ['nullable','numeric','between:-180,180'],
            'status' => ['required','string', Rule::in(Unit::STATUSES)],
            'listing_type' => ['required','string', Rule::in(Unit::LISTING_TYPES)],
            'photos' => ['nullable','array','max:50'],
            'photos.*' => ['image','mimes:jpeg,png,jpg,webp','max:5120'],
            'keep_photos_present' => ['nullable'],
            'keep_photos' => ['nullable', 'array'],
            'keep_photos.*' => ['string', 'max:2048'],
            'location' => ['nullable','string','max:500'],
            'location_url' => ['nullable','string','max:2000'],
            'attributes' => ['nullable','array'],
            'agent_ids' => ['nullable','array'],
            'agent_ids.*' => ['integer','exists:agents,id'],
        ];
    }
}
