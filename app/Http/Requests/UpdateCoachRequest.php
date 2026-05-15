<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoachRequest extends FormRequest
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
            'deskripsi_keahlian' => ['required', 'string', 'max:1000'],
            'harga_per_jam' => ['required', 'integer', 'min:10000'],
            'availability_status' => ['required', 'in:active,inactive,on_leave'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // max 5MB, optional
            'schedule' => ['required', 'array'],
            'schedule.mon' => ['required', 'boolean'],
            'schedule.tue' => ['required', 'boolean'],
            'schedule.wed' => ['required', 'boolean'],
            'schedule.thu' => ['required', 'boolean'],
            'schedule.fri' => ['required', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'deskripsi_keahlian.required' => 'Deskripsi keahlian wajib diisi',
            'harga_per_jam.required' => 'Harga per jam wajib diisi',
            'harga_per_jam.min' => 'Harga per jam minimal Rp 10.000',
            'availability_status.required' => 'Status ketersediaan wajib dipilih',
            'availability_status.in' => 'Status ketersediaan tidak valid',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'photo.max' => 'Ukuran gambar maksimal 5MB',
            'schedule.required' => 'Jadwal mingguan wajib diisi',
        ];
    }
}
