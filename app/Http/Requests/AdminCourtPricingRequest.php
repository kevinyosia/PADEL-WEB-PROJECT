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
            'harga_weekend' => ['required', 'integer', 'min:1', 'max:9999999'],
            'harga_weekend_prime' => ['required', 'integer', 'min:1', 'max:9999999'],
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
            'harga_weekend.required' => 'Harga weekend normal wajib diisi',
            'harga_weekend.integer' => 'Harga weekend normal harus berupa angka',
            'harga_weekend.min' => 'Harga weekend normal harus lebih dari 0',
            'harga_weekend.max' => 'Harga weekend normal terlalu besar',
            'harga_weekend_prime.required' => 'Harga weekend prime time wajib diisi',
            'harga_weekend_prime.integer' => 'Harga weekend prime time harus berupa angka',
            'harga_weekend_prime.min' => 'Harga weekend prime time harus lebih dari 0',
            'harga_weekend_prime.max' => 'Harga weekend prime time terlalu besar',
        ];
    }
}
