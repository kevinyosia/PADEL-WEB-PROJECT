<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';
    protected $fillable = ['user_id', 'reservation_id', 'rating', 'komentar_review'];

    public function user() { return $this->belongsTo(User::class); }
    public function reservation() { return $this->belongsTo(Reservation::class); }
}