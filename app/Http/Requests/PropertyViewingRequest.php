<?php

namespace App\Http\Requests;

use App\Models\PropertyViewing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyViewingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_id' => ['required','exists:agent_leads,id'],
            'property_id' => ['required','exists:properties,id'],
            'agent_id' => ['required','exists:agents,id'],
            'appointment_at' => ['required','date'],
            'status' => ['required', Rule::in(PropertyViewing::STATUSES)],
            'notes' => ['nullable','string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($agentId = auth()->user()?->agent_id) {
            $this->merge(['agent_id' => $agentId]);
        }
    }
}
