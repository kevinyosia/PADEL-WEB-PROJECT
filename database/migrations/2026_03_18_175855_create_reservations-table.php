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
        Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('court_id')->constrained('courts')->cascadeOnDelete();
        $table->foreignId('coach_id')->nullable()->constrained('coaches')->nullOnDelete();
        
        $table->date('tanggal_booking');
        $table->time('jam_mulai');
        $table->time('jam_selesai');
        $table->enum('status_reservasi', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
