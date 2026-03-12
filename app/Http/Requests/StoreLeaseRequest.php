<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Lease::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'property_id' => ['nullable','integer','exists:properties,id'],
            'unit_id' => ['required','integer','exists:units,id'],
            'start_date' => ['required','date'],
            'end_date' => ['nullable','date','after:start_date'],
            'rent_cents' => ['required','integer','min:0'],
            'deposit_cents' => ['nullable','integer','min:0'],
            'resident_ids' => ['required','array','min:1'],
            'resident_ids.*' => ['integer','exists:residents,id'],
        ];
    }
}
