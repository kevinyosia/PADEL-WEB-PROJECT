<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $reservation_id
 * @property int|null $total_harga_lapangan
 * @property int|null $total_harga_coach
 * @property int|null $total_harga_perlengkapan
 * @property int|null $potongan_poin
 * @property int $grand_total
 * @property string $metode_pembayaran
 * @property string|null $channel_pembayaran
 * @property string $status_pembayaran
 * @property string|null $snap_token
 * @property string|null $midtrans_order_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['reservation_id', 'total_harga_lapangan', 'total_harga_coach', 'total_harga_perlengkapan', 'potongan_poin', 'grand_total', 'metode_pembayaran', 'channel_pembayaran', 'status_pembayaran', 'snap_token', 'midtrans_order_id'];

    public function reservation() { return $this->belongsTo(Reservation::class); }
}