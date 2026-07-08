<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            if (! Schema::hasColumn('courts', 'harga_weekend_prime')) {
                $table->integer('harga_weekend_prime')->default(350000)->after('harga_weekend');
            }
        });

        DB::table('courts')->update([
            'harga_pagi_tengahmalam' => 245000,
            'harga_malam' => 295000,
            'harga_weekend' => 275000,
            'harga_weekend_prime' => 350000,
        ]);
    }

    public function down(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            if (Schema::hasColumn('courts', 'harga_weekend_prime')) {
                $table->dropColumn('harga_weekend_prime');
            }
        });
    }
};
