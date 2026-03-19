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
        Schema::create('coach_review', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Coach mana yang dinilai
            $table->foreignId('coach_id')->constrained('coaches')->cascadeOnDelete();
            // (Opsional) Berdasarkan reservasi yang mana agar tidak ada fake review
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            
            $table->integer('rating'); // Bintang 1 sampai 5
            $table->text('review')->nullable(); // Ulasan teks
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coach_review');
    }
};
