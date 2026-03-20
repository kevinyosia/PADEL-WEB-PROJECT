<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'court_id', 'coach_id', 'tanggal_booking', 'jam_mulai', 'jam_selesai', 'status_reservasi'];

    public function user() { return $this->belongsTo(User::class); }
    public function court() { return $this->belongsTo(Court::class); }
    public function coach() { return $this->belongsTo(Coach::class); }
    
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'reservation_uqipment', 'reservation_id', 'equipment_id')
                    ->withPivot('jumlah_sewa', 'subtotal_harga')
                    ->withTimestamps();
    }

    public function transaction() { return $this->hasOne(Transaction::class); }
}