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
        ];

    public function reservations() { return $this->hasMany(Reservation::class); }
    public function reviews() { return $this->hasMany(CoachReview::class); }
}