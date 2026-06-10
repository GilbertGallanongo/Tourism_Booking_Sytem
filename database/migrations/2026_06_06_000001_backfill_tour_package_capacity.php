<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tour_packages') || ! Schema::hasColumn('tour_packages', 'max_guests')) {
            return;
        }

        DB::table('tour_packages')
            ->whereNull('max_guests')
            ->orWhere('max_guests', '<', 1)
            ->update(['max_guests' => 10]);
    }

    public function down(): void
    {
        // Backfilled values are intentionally kept.
    }
};
