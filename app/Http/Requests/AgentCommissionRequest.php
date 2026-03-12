<?php

namespace App\Http\Requests;

use App\Models\AgentCommission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgentCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agent_id' => ['required','exists:agents,id'],
            'lease_id' => ['nullable','exists:leases,id'],
            'amount' => ['required','numeric','min:0'],
            'rate' => ['nullable','numeric','min:0','max:100'],
            'status' => ['required', Rule::in(AgentCommission::STATUSES)],
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
