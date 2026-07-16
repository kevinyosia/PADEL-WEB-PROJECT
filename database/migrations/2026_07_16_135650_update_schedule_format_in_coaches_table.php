<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const DAYS = ['mon', 'tue', 'wed', 'thu', 'fri'];

    /**
     * Convert schedule format from {mon: bool} to {mon: {active: bool, sessions: []}}.
     */
    public function up(): void
    {
        DB::table('coaches')->lazyById()->each(function (object $coach) {
            $old = json_decode($coach->schedule ?? '{}', true) ?? [];
            $new = [];

            foreach (self::DAYS as $day) {
                $value = $old[$day] ?? false;

                // Already in new format — skip
                if (is_array($value)) {
                    $new[$day] = $value;

                    continue;
                }

                $new[$day] = [
                    'active' => (bool) $value,
                    'sessions' => [],
                ];
            }

            DB::table('coaches')
                ->where('id', $coach->id)
                ->update(['schedule' => json_encode($new)]);
        });
    }

    /**
     * Convert schedule format back to simple boolean {mon: bool}.
     */
    public function down(): void
    {
        DB::table('coaches')->lazyById()->each(function (object $coach) {
            $new = json_decode($coach->schedule ?? '{}', true) ?? [];
            $old = [];

            foreach (self::DAYS as $day) {
                $value = $new[$day] ?? ['active' => false];
                $old[$day] = is_array($value) ? (bool) ($value['active'] ?? false) : (bool) $value;
            }

            DB::table('coaches')
                ->where('id', $coach->id)
                ->update(['schedule' => json_encode($old)]);
        });
    }
};
