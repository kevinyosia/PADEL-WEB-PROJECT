<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add weekly schedule dan availability status untuk coach management.
     * 
     * - availability_status: enum (active, inactive, on_leave)
     * - schedule: json {mon: true, tue: true, wed: false, thu: true, fri: true}
     */
    public function up(): void
    {
        Schema::table('coaches', function (Blueprint $table) {
            // Availability status: active (siap teaching), inactive (tidak siap), on_leave (cuti)
            $table->enum('availability_status', ['active', 'inactive', 'on_leave'])
                ->default('inactive')
                ->after('harga_per_jam');
            
            // Weekly schedule as JSON: {mon, tue, wed, thu, fri} = true/false
            $table->json('schedule')->default('{"mon":true,"tue":true,"wed":true,"thu":true,"fri":true}')
                ->after('availability_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coaches', function (Blueprint $table) {
            $table->dropColumn(['availability_status', 'schedule']);
        });
    }
};
