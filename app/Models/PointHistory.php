<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;

    protected $table = 'points_histories';
    protected $fillable = ['user_id', 'jumlah_poin', 'keterangan'];

    public function user() { return $this->belongsTo(User::class); }
}