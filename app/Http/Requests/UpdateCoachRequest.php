<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoachRequest extends FormRequest
{
    private const SCHEDULE_DAYS = ['mon', 'tue', 'wed', 'thu', 'fri'];

    private const MAX_SESSIONS_PER_DAY = 3;

    private const MIN_SESSION_HOURS = 1;

    private const MAX_SESSION_HOURS = 4;

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
        $rules = [
            'deskripsi_keahlian' => ['required', 'string', 'max:1000'],
            'harga_per_jam' => ['required', 'integer', 'min:10000'],
            'availability_status' => ['required', 'in:active,inactive,on_leave'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'schedule' => ['required', 'array'],
        ];

        foreach (self::SCHEDULE_DAYS as $day) {
            $rules["schedule.{$day}"] = ['nullable', 'array'];
            $rules["schedule.{$day}.active"] = ['nullable', 'boolean'];
            $rules["schedule.{$day}.sessions"] = ['nullable', 'array', 'max:'.self::MAX_SESSIONS_PER_DAY];
            $rules["schedule.{$day}.sessions.*.start"] = ['required_with:schedule.'.$day.'.sessions.*', 'date_format:H:i'];
            $rules["schedule.{$day}.sessions.*.end"] = ['required_with:schedule.'.$day.'.sessions.*', 'date_format:H:i'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $schedule = $this->input('schedule', []);

        foreach (self::SCHEDULE_DAYS as $day) {
            $dayData = $schedule[$day] ?? [];
            $active = filter_var($dayData['active'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $sessions = [];

            foreach ($dayData['sessions'] ?? [] as $session) {
                $sessions[] = [
                    'start' => $session['start'] ?? '',
                    'end' => $session['end'] ?? '',
                ];
            }

            $schedule[$day] = [
                'active' => $active,
                'sessions' => $sessions,
            ];
        }

        $this->merge(['schedule' => $schedule]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $schedule = $this->input('schedule', []);
            $hasActiveDay = false;

            foreach (self::SCHEDULE_DAYS as $day) {
                $dayData = $schedule[$day] ?? [];
                $active = (bool) ($dayData['active'] ?? false);
                $sessions = $dayData['sessions'] ?? [];

                if (! $active) {
                    continue;
                }

                $hasActiveDay = true;

                if (empty($sessions)) {
                    $validator->errors()->add(
                        "schedule.{$day}.sessions",
                        "Hari {$day} aktif tapi tidak memiliki sesi. Tambahkan minimal 1 sesi."
                    );

                    continue;
                }

                $this->validateSessions($validator, $day, $sessions);
            }

            if (! $hasActiveDay) {
                $validator->errors()->add('schedule', 'Pilih minimal 1 hari jadwal coach.');
            }
        });
    }

    /**
     * Validate individual sessions for a day: end > start, duration 1–4 hours, no overlap.
     *
     * @param  list<array{start: string, end: string}>  $sessions
     */
    private function validateSessions($validator, string $day, array $sessions): void
    {
        $intervals = [];

        foreach ($sessions as $index => $session) {
            $start = $session['start'] ?? '';
            $end = $session['end'] ?? '';

            if (! $start || ! $end) {
                continue;
            }

            $startMinutes = $this->timeToMinutes($start);
            $endMinutes = $this->timeToMinutes($end);

            if ($endMinutes <= $startMinutes) {
                $validator->errors()->add(
                    "schedule.{$day}.sessions.{$index}.end",
                    'Sesi '.($index + 1)." hari {$day}: jam selesai harus setelah jam mulai."
                );

                continue;
            }

            $durationHours = ($endMinutes - $startMinutes) / 60;

            if ($durationHours < self::MIN_SESSION_HOURS) {
                $validator->errors()->add(
                    "schedule.{$day}.sessions.{$index}.end",
                    'Sesi '.($index + 1)." hari {$day}: durasi minimal ".self::MIN_SESSION_HOURS.' jam.'
                );

                continue;
            }

            if ($durationHours > self::MAX_SESSION_HOURS) {
                $validator->errors()->add(
                    "schedule.{$day}.sessions.{$index}.end",
                    'Sesi '.($index + 1)." hari {$day}: durasi maksimal ".self::MAX_SESSION_HOURS.' jam.'
                );

                continue;
            }

            foreach ($intervals as $existing) {
                if ($startMinutes < $existing['end'] && $endMinutes > $existing['start']) {
                    $validator->errors()->add(
                        "schedule.{$day}.sessions.{$index}.start",
                        'Sesi '.($index + 1)." hari {$day}: waktu sesi bertabrakan dengan sesi lain."
                    );
                    break;
                }
            }

            $intervals[] = ['start' => $startMinutes, 'end' => $endMinutes];
        }
    }

    /**
     * Convert HH:MM string to total minutes.
     */
    private function timeToMinutes(string $time): int
    {
        [$hours, $minutes] = array_map('intval', explode(':', $time));

        return ($hours * 60) + $minutes;
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'deskripsi_keahlian.required' => 'Deskripsi keahlian wajib diisi.',
            'harga_per_jam.required' => 'Harga per jam wajib diisi.',
            'harga_per_jam.min' => 'Harga per jam minimal Rp 10.000.',
            'availability_status.required' => 'Status ketersediaan wajib dipilih.',
            'availability_status.in' => 'Status ketersediaan tidak valid.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'photo.max' => 'Ukuran gambar maksimal 5MB.',
            'schedule.required' => 'Jadwal mingguan wajib diisi.',
            '*.sessions.max' => 'Maksimal '.self::MAX_SESSIONS_PER_DAY.' sesi per hari.',
            '*.sessions.*.start.date_format' => 'Format jam mulai harus HH:MM.',
            '*.sessions.*.end.date_format' => 'Format jam selesai harus HH:MM.',
        ];
    }
}
