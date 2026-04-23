<?php
namespace App\Models;
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
        'status'
    ];
    public function reservations() { return $this->hasMany(Reservation::class); }
}