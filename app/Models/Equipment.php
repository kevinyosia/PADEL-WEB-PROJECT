<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment'; 
    protected $fillable = ['nama_alat', 'harga_sewa', 'stok'];

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_uqipment', 'equipment_id', 'reservation_id')
                    ->withPivot('jumlah_sewa', 'subtotal_harga')
                    ->withTimestamps();
    }
}