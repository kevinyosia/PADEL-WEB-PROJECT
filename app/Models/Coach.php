<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 
        'deskripsi_keahlian', 
        'harga_per_jam',
        'photo',
        'availability_status',
        'schedule',
    ];

    protected $casts = [
        'schedule' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
    public function reviews() { return $this->hasMany(CoachReview::class); }

    /**
     * Get availability status display name
     */
    public function getAvailabilityLabel(): string
    {
        return match($this->availability_status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
            default => 'Inactive',
        };
    }

    /**
     * Get availability status color
     */
    public function getAvailabilityColor(): string
    {
        return match($this->availability_status) {
            'active' => 'green',
            'inactive' => 'gray',
            'on_leave' => 'amber',
            default => 'gray',
        };
    }

    /**
     * Check if coach available on specific day (mon, tue, wed, thu, fri)
     */
    public function isAvailableOnDay(string $day): bool
    {
        return $this->schedule[$day] ?? false;
    }

    /**
     * Get active days count
     */
    public function getActiveDaysCount(): int
    {
        return count(array_filter($this->schedule ?? []));
    }
}