<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','array'],
            'name.en' => ['required','string','max:255'],
            'name.ar' => ['nullable','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:50'],
            'license_id' => ['nullable','string','max:255'],
            'commission_rate' => ['nullable','numeric','min:0','max:100'],
            'active' => ['boolean'],
            'photo' => ['nullable','image','max:2048'],
        ];
    }
}
