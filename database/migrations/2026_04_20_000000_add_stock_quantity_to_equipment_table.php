<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment', 'stock_quantity')) {
                if (Schema::hasColumn('equipment', 'harga')) {
                    $table->integer('stock_quantity')->default(0)->after('harga');
                } else {
                    $table->integer('stock_quantity')->default(0);
                }
            }
            
            // Add merged RentalItem fields
            if (!Schema::hasColumn('equipment', 'sku')) {
                $table->string('sku')->nullable()->unique()->after('nama_alat');
            }
            if (!Schema::hasColumn('equipment', 'max_capacity')) {
                $table->integer('max_capacity')->default(0)->after('stock_quantity');
            }
            if (!Schema::hasColumn('equipment', 'condition')) {
                $table->enum('condition', ['excellent', 'good', 'maintenance'])->default('good')->after('max_capacity');
            }
            if (!Schema::hasColumn('equipment', 'rental_rate')) {
                $table->integer('rental_rate')->nullable()->after('condition');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'sku', 'max_capacity', 'condition', 'rental_rate']);
        });
    }
};
