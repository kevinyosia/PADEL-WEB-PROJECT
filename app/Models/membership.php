<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $table = 'membership';

    // Membership tracking: poin rewards dan status member
    protected $fillable = ['user_id', 'total_poin_aktif', 'total_poin_terpakai'];

    public function user() { return $this->belongsTo(User::class); }
}