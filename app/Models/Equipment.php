<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment'; 
    protected $fillable = [
        'nama_alat',
        'kategori',
        'harga',
        'deskripsi'
    ];

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_equipment', 'equipment_id', 'reservation_id')
                    ->withPivot('jumlah_sewa', 'subtotal_harga')
                    ->withTimestamps();
    }
}