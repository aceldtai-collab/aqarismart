<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Property::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','array'],
            'name.en' => ['required','string','max:255'],
            'name.ar' => ['nullable','string','max:255'],
            'description' => ['nullable','array'],
            'description.en' => ['nullable','string'],
            'description.ar' => ['nullable','string'],
            'category_id' => ['nullable','integer','exists:categories,id'],
            'address' => ['nullable','string','max:255'],
            'city' => ['nullable','array'],
            'city.en' => ['nullable','string','max:255'],
            'city.ar' => ['nullable','string','max:255'],
            'state' => ['nullable','string','max:100'],
            'postal' => ['nullable','string','max:20'],
            'country' => ['nullable','string','max:2'],
            'country_id' => ['nullable','integer','exists:countries,id'],
            'state_id' => ['nullable','integer','exists:states,id'],
            'city_id' => ['nullable','integer','exists:cities,id'],
            'photos' => ['sometimes','array'],
            'photos.*' => ['file','image','max:5120'],
            'agent_ids' => ['nullable','array'],
            'agent_ids.*' => ['integer','exists:agents,id'],
        ];
    }
}
