<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'rental_rate',
        'stock_quantity',
        'max_capacity',
        'condition',
    ];

    protected $casts = [
        'rental_rate' => 'integer',
        'stock_quantity' => 'integer',
        'max_capacity' => 'integer',
    ];

    /**
     * Get utilization percentage
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
