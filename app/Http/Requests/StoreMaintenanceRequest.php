<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\MaintenanceRequest::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'property_id' => ['nullable','integer','exists:properties,id'],
            'unit_id' => ['nullable','integer','exists:units,id'],
            'resident_id' => ['nullable','integer','exists:residents,id'],
            'title' => ['required','string','max:255'],
            'details' => ['nullable','string'],
            'priority' => ['nullable','string','in:low,normal,high'],
        ];
    }
}
