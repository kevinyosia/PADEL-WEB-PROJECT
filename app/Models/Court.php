<?php
namespace App\Models;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lapangan',
        'deskripsi',
        'harga_pagi_tengahmalam',
        'harga_malam',
        'harga_weekend',
        'harga_weekend_prime',
        'status'
    ];

    public function hourlyRateFor(CarbonInterface $slotStart): int
    {
        $isPrimeTime = $slotStart->hour >= 16 && $slotStart->hour < 22;

        if ($slotStart->isWeekend()) {
            return $isPrimeTime
                ? (int) $this->harga_weekend_prime
                : (int) $this->harga_weekend;
        }

        return $isPrimeTime
            ? (int) $this->harga_malam
            : (int) $this->harga_pagi_tengahmalam;
    }

    public function priceForRange(CarbonInterface $start, CarbonInterface $end): int
    {
        $cursor = $start->copy();
        $total = 0;

        while ($cursor < $end) {
            $total += $this->hourlyRateFor($cursor);
            $cursor->addHour();
        }

        return $total;
    }

    public function reservations() { return $this->hasMany(Reservation::class); }
}
