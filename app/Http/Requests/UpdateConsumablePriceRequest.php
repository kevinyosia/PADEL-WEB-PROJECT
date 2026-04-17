<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsumablePriceRequest extends FormRequest
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
            'purchase_price' => ['required', 'integer', 'min:1', 'max:9999999'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'purchase_price.required' => 'Harga beli wajib diisi',
            'purchase_price.integer' => 'Harga beli harus berupa angka',
            'purchase_price.min' => 'Harga beli harus lebih dari 0',
            'purchase_price.max' => 'Harga beli terlalu besar',
        ];
    }
}
