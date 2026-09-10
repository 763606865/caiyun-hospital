<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'contact' => ['required', 'string', 'max:100'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'organization_type' => ['nullable', Rule::in(['tcm_clinic', 'community_clinic', 'general_clinic', 'chain_clinic', 'other'])],
            'request_type' => ['required', Rule::in(['consultation', 'trial'])],
            'requirements' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'prohibited'],
        ];
    }
}
