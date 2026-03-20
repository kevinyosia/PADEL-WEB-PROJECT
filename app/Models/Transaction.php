<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['reservation_id', 'total_bayar', 'metode_pembayaran', 'status_pembayaran'];

    public function reservation() { return $this->belongsTo(Reservation::class); }
}