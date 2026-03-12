<?php

namespace App\Http\Requests;

use App\Models\AgentLead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgentLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agent_id' => ['required','exists:agents,id'],
            'name' => ['required','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:50'],
            'source' => ['nullable','string','max:255'],
            'status' => ['required', Rule::in(AgentLead::STATUSES)],
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
