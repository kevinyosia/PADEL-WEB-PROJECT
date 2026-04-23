<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCourtPricingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin users can update pricing
        // TODO: Implement admin role check when auth system is expanded
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'harga_pagi_tengahmalam' => ['required', 'integer', 'min:1', 'max:9999999'],
            'harga_malam' => ['required', 'integer', 'min:1', 'max:9999999'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'harga_pagi_tengahmalam.required' => 'Harga pagi - tengah malam wajib diisi',
            'harga_pagi_tengahmalam.integer' => 'Harga pagi - tengah malam harus berupa angka',
            'harga_pagi_tengahmalam.min' => 'Harga pagi - tengah malam harus lebih dari 0',
            'harga_pagi_tengahmalam.max' => 'Harga pagi - tengah malam terlalu besar',
            'harga_malam.required' => 'Harga malam (18:00+) wajib diisi',
            'harga_malam.integer' => 'Harga malam harus berupa angka',
            'harga_malam.min' => 'Harga malam harus lebih dari 0',
            'harga_malam.max' => 'Harga malam terlalu besar',
        ];
    }
}
