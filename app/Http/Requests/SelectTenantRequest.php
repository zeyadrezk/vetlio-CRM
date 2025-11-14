<?php

namespace App\Http\Requests;

use App\Models\Organisation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Select Tenant Request
 *
 * Validates tenant selection requests with comprehensive validation rules:
 * - Ensures organisation exists
 * - Ensures organisation is active
 * - Ensures organisation has at least one branch
 */
class SelectTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // In production, you might want to add authorization logic
        // For now, allow in local/development environments
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'organisation_id' => [
                'required',
                'integer',
                Rule::exists('organisations', 'id')->where(function ($query) {
                    $query->where('active', true);
                }),
                function ($attribute, $value, $fail) {
                    // Ensure organisation has at least one branch
                    $organisation = Organisation::with('branches')->find($value);

                    if ($organisation && $organisation->branches->isEmpty()) {
                        $fail('The selected organisation has no branches available.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'organisation_id.required' => 'Please select an organisation.',
            'organisation_id.integer' => 'Invalid organisation selection.',
            'organisation_id.exists' => 'The selected organisation is not available or has been deactivated.',
        ];
    }

    /**
     * Get the validated organisation
     */
    public function getOrganisation(): Organisation
    {
        return Organisation::query()
            ->whereActive(true)
            ->with('branches')
            ->findOrFail($this->validated('organisation_id'));
    }
}
