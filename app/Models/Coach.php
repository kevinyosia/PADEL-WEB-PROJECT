<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $deskripsi_keahlian
 * @property int $harga_per_jam
 * @property string|null $photo
 * @property string $availability_status
 * @property array<string, array{active: bool, sessions: list<array{start: string, end: string}>}>|null $schedule
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Coach extends Model
{
    use HasFactory;

    public const SCHEDULE_DAYS = ['mon', 'tue', 'wed', 'thu', 'fri'];

    protected $fillable = [
        'user_id',
        'deskripsi_keahlian',
        'harga_per_jam',
        'photo',
        'availability_status',
        'schedule',
    ];

    protected function casts(): array
    {
        return [
            'schedule' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(CoachReview::class);
    }

    /**
     * Get availability status display name.
     */
    public function getAvailabilityLabel(): string
    {
        return match ($this->availability_status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
            default => 'Inactive',
        };
    }

    /**
     * Get availability status color.
     */
    public function getAvailabilityColor(): string
    {
        return match ($this->availability_status) {
            'active' => 'green',
            'inactive' => 'gray',
            'on_leave' => 'amber',
            default => 'gray',
        };
    }

    /**
     * Check if coach is active on a specific day (mon, tue, wed, thu, fri).
     */
    public function isAvailableOnDay(string $day): bool
    {
        $dayData = $this->schedule[$day] ?? null;

        if (is_array($dayData)) {
            return (bool) ($dayData['active'] ?? false);
        }

        return (bool) $dayData;
    }

    /**
     * Get the list of sessions for a specific day.
     *
     * @return list<array{start: string, end: string}>
     */
    public function getSessionsForDay(string $day): array
    {
        $dayData = $this->schedule[$day] ?? null;

        if (! is_array($dayData)) {
            return [];
        }

        return $dayData['sessions'] ?? [];
    }

    /**
     * Get the total number of active sessions across all days.
     */
    public function getSessionCount(): int
    {
        $total = 0;

        foreach ($this->schedule ?? [] as $dayData) {
            if (is_array($dayData) && ($dayData['active'] ?? false)) {
                $total += count($dayData['sessions'] ?? []);
            }
        }

        return $total;
    }

    /**
     * Get number of active days.
     */
    public function getActiveDaysCount(): int
    {
        return collect($this->schedule ?? [])->filter(function ($dayData) {
            if (is_array($dayData)) {
                return (bool) ($dayData['active'] ?? false);
            }

            return (bool) $dayData;
        })->count();
    }

    /**
     * Check whether a given time (HH:MM) falls within any session slot for a day.
     */
    public function isTimeInSession(string $day, string $time): bool
    {
        foreach ($this->getSessionsForDay($day) as $session) {
            if ($time >= $session['start'] && $time < $session['end']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether a time range is fully contained in one session slot for a day.
     */
    public function isRangeWithinAnySession(string $day, string $start, string $end): bool
    {
        foreach ($this->getSessionsForDay($day) as $session) {
            if ($start >= $session['start'] && $end <= $session['end'] && $start < $end) {
                return true;
            }
        }

        return false;
    }
}
