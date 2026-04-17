<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourtStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Implement admin role check when auth system is expanded
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in('tersedia', 'maintenance', 'pembersihan'),
            ],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status lapangan wajib dipilih',
            'status.in' => 'Status lapangan harus salah satu dari: Tersedia, Maintenance, atau Pembersihan',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'status' => 'status lapangan',
        ];
    }
}
