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
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lapangan');
            $table->text('deskripsi')->nullable();
            $table->integer('harga_pagi_tengahmalam');
            $table->integer('harga_malam');
            $table->integer('harga_weekend');
            $table->enum('status', ['tersedia', 'maintenance', 'pembersihan'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
