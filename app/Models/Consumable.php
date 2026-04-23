<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'purchase_price',
        'stock_quantity',
        'max_capacity',
    ];

    protected $casts = [
        'purchase_price' => 'integer',
        'stock_quantity' => 'integer',
        'max_capacity' => 'integer',
    ];

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
     * Get stock status color for UI
     */
    public function getStatusColor(): string
    {
        return match($this->getStockStatus()) {
            'IN STOCK' => 'green',
            'LOW STOCK' => 'yellow',
            'OUT OF STOCK' => 'red',
            default => 'gray',
        };
    }
}
