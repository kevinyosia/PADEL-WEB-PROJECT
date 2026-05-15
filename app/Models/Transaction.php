<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['reservation_id', 'total_harga_lapangan', 'total_harga_coach', 'total_harga_perlengkapan', 'potongan_poin', 'grand_total', 'metode_pembayaran', 'channel_pembayaran', 'status_pembayaran', 'snap_token', 'midtrans_order_id'];

    public function reservation() { return $this->belongsTo(Reservation::class); }
}