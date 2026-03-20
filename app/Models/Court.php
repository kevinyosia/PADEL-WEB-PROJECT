<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    protected $fillable = ['nama_lapangan', 'tipe_lapangan', 'harga_per_jam', 'deskripsi'];

    public function reservations() { return $this->hasMany(Reservation::class); }
}