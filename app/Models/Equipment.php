<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment'; 
    protected $fillable = [
        'nama_alat',
        'sku',
        'kategori',
        'harga',
        'deskripsi',
        'stock_quantity',
        'max_capacity',
        'condition',
        'rental_rate',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'max_capacity' => 'integer',
        'rental_rate' => 'integer',
    ];

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_equipment', 'equipment_id', 'reservation_id')
                    ->withPivot('jumlah_sewa', 'subtotal_harga')
                    ->withTimestamps();
    }

    /**
     * Get utilization percentage (for rental items)
     */
    public function getUtilizationPercentage(): int
    {
        if ($this->max_capacity == 0) {
            return 0;
        }
        return (int) (($this->stock_quantity / $this->max_capacity) * 100);
    }

    /**
     * Get stock status: IN STOCK (>50), LOW STOCK (1-50), OUT OF STOCK (0)
     */
    public function getStockStatus(): string
    {
        if ($this->stock_quantity == 0) {
            return 'OUT OF STOCK';
        }
        if ($this->stock_quantity <= 50) {
            return 'LOW STOCK';
        }
        return 'IN STOCK';
    }

    /**
     * Get condition badge color
     */
    public function getConditionColor(): string
    {
        return match($this->condition) {
            'excellent' => 'green',
            'good' => 'blue',
            'maintenance' => 'yellow',
            default => 'gray',
        };
    }
}