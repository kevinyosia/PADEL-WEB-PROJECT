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
        
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        
        $table->unsignedBigInteger('court_id');
        $table->foreign('court_id')->references('id')->on('courts')->onDelete('cascade');
        
        $table->unsignedBigInteger('coach_id')->nullable();
        $table->foreign('coach_id')->references('id')->on('coaches')->onDelete('set null');

        $table->date('tanggal_booking');
        $table->time('jam_mulai');
        $table->time('jam_selesai');
        $table->enum('status_reservasi', ['confirmed', 'completed', 'cancelled'])->default('confirmed');
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
