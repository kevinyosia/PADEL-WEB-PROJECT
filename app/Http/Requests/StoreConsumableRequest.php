<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsumableRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:consumables,sku'],
            'description' => ['nullable', 'string', 'max:1000'],
            'purchase_price' => ['required', 'integer', 'min:1', 'max:9999999'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:9999999'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:9999999'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama barang wajib diisi',
            'name.max' => 'Nama barang maksimal 255 karakter',
            'sku.required' => 'SKU wajib diisi',
            'sku.unique' => 'SKU sudah digunakan',
            'purchase_price.required' => 'Harga beli wajib diisi',
            'purchase_price.integer' => 'Harga beli harus berupa angka',
            'purchase_price.min' => 'Harga beli harus lebih dari 0',
            'stock_quantity.required' => 'Jumlah stok wajib diisi',
            'stock_quantity.integer' => 'Jumlah stok harus berupa angka',
            'max_capacity.required' => 'Kapasitas maksimal wajib diisi',
            'max_capacity.min' => 'Kapasitas minimal harus 1',
        ];
    }
}
