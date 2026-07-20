<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipmentStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $equipment = $this->route('equipment') ?? $this->route('consumable');
        $maxCapacity = $equipment ? (int) $equipment->max_capacity : 9999999;

        return [
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:' . max(0, $maxCapacity)],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'stock_quantity.required' => 'Jumlah stok wajib diisi',
            'stock_quantity.integer' => 'Jumlah stok harus berupa angka',
            'stock_quantity.min' => 'Jumlah stok tidak boleh negatif',
            'stock_quantity.max' => 'Jumlah stok tidak boleh melebihi kapasitas maksimal (:max)',
        ];
    }
}
