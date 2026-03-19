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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->integer('total_harga_lapangan');
            $table->integer('total_harga_coach')->default(0);
            $table->integer('total_harga_perlengkapan')->default(0);
            $table->integer('grand_total');
            $table->enum('metode_pembayaran', ['cash', 'transfer'])->nullable();
            $table->enum('status_pembayaran', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
