<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoachRequest extends FormRequest
{
    private const SCHEDULE_DAYS = ['mon', 'tue', 'wed', 'thu', 'fri'];

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
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'deskripsi_keahlian' => ['required', 'string', 'max:1000'],
            'harga_per_jam' => ['required', 'integer', 'min:10000'],
            'availability_status' => ['required', 'in:active,inactive,on_leave'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // max 5MB, optional
            'schedule' => ['required', 'array'],
            'schedule.mon' => ['nullable', 'boolean'],
            'schedule.tue' => ['nullable', 'boolean'],
            'schedule.wed' => ['nullable', 'boolean'],
            'schedule.thu' => ['nullable', 'boolean'],
            'schedule.fri' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $schedule = $this->input('schedule', []);

        foreach (self::SCHEDULE_DAYS as $day) {
            $schedule[$day] = filter_var($schedule[$day] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        $this->merge(['schedule' => $schedule]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $selectedDays = collect($this->input('schedule', []))
                ->only(self::SCHEDULE_DAYS)
                ->filter()
                ->count();

            if ($selectedDays < 1) {
                $validator->errors()->add('schedule', 'Pilih minimal 1 hari jadwal coach');
            }
        });
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama coach wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon wajib diisi',
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
