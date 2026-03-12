<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Resident::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required','string','max:100'],
            'last_name' => ['required','string','max:100'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:50'],
            'notes' => ['nullable','string'],
        ];
    }
}

