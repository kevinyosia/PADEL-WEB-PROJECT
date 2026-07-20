<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentRequest extends FormRequest
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
            'nama_alat' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:equipment,sku'],
            'kategori' => ['required', 'in:sewa,beli'],
            'harga' => ['required', 'integer', 'min:1', 'max:9999999'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:9999999', 'lte:max_capacity'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:9999999'],
            'condition' => ['required', 'in:excellent,good,maintenance'],
            'rental_rate' => ['nullable', 'integer', 'min:1', 'max:9999999'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'nama_alat.required' => 'Nama barang wajib diisi',
            'nama_alat.max' => 'Nama barang maksimal 255 karakter',
            'sku.required' => 'SKU wajib diisi',
            'sku.unique' => 'SKU sudah digunakan',
            'kategori.required' => 'Kategori wajib dipilih',
            'kategori.in' => 'Kategori harus berupa sewa atau beli',
            'harga.required' => 'Harga wajib diisi',
            'harga.integer' => 'Harga harus berupa angka',
            'harga.min' => 'Harga harus lebih dari 0',
            'stock_quantity.required' => 'Jumlah stok wajib diisi',
            'stock_quantity.integer' => 'Jumlah stok harus berupa angka',
            'stock_quantity.lte' => 'Jumlah stok tidak boleh melebihi kapasitas maksimal',
            'max_capacity.required' => 'Kapasitas maksimal wajib diisi',
            'max_capacity.min' => 'Kapasitas minimal harus 1',
            'condition.required' => 'Kondisi wajib dipilih',
            'condition.in' => 'Kondisi harus berupa excellent, good, atau maintenance',
            'rental_rate.integer' => 'Harga rental harus berupa angka',
            'rental_rate.min' => 'Harga rental harus lebih dari 0',
        ];
    }

    /**
     * Get the data being validated.
     */
    protected function prepareForValidation()
    {
        // Set rental_rate to null if kategori is 'beli' and no rental_rate provided
        if ($this->get('kategori') === 'beli') {
            $this->merge([
                'rental_rate' => null,
            ]);
        }

        // Set harga as the price (use rename if needed)
        if (!$this->has('harga') && $this->has('price')) {
            $this->merge([
                'harga' => $this->get('price'),
            ]);
        }
    }
}
