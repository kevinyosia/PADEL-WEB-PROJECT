<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    // Sesuaikan kolom ini dengan migration membership Anda
    protected $fillable = ['user_id', 'jenis_membership', 'tanggal_mulai', 'tanggal_berakhir', 'status'];

    public function user() { return $this->belongsTo(User::class); }
}