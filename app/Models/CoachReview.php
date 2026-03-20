<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachReview extends Model
{
    use HasFactory;
    
    protected $table = 'coach_review'; // Sesuaikan jika nama tabel Anda berbeda
    protected $fillable = ['user_id', 'coach_id', 'rating', 'komentar'];

    public function user() { return $this->belongsTo(User::class); }
    public function coach() { return $this->belongsTo(Coach::class); }
}