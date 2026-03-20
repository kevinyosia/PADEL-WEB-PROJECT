<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    use HasFactory;
    
    protected $fillable = ['nama_coach', 'spesialisasi', 'harga_per_jam', 'kontak'];

    public function reservations() { return $this->hasMany(Reservation::class); }
    public function reviews() { return $this->hasMany(CoachReview::class); }
}