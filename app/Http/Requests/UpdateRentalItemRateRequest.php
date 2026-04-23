<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalItemRateRequest extends FormRequest
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
            'rental_rate' => ['required', 'integer', 'min:1', 'max:9999999'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'rental_rate.required' => 'Harga rental wajib diisi',
            'rental_rate.integer' => 'Harga rental harus berupa angka',
            'rental_rate.min' => 'Harga rental harus lebih dari 0',
            'rental_rate.max' => 'Harga rental terlalu besar',
        ];
    }
}
