<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getConfig('driver');

        if ($driver === 'sqlite') {
            // SQLite does not support DROP CONSTRAINT / ADD CONSTRAINT on existing tables.
            return;
        }

        DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_status_check;");
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_status_check CHECK (status IN ('pending','confirmed','cancelled','completed','approved','declined','cancellation_pending')); ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getConfig('driver');

        if ($driver === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_status_check;");
        DB::statement("ALTER TABLE bookings ADD CONSTRAINT bookings_status_check CHECK (status IN ('pending','confirmed','cancelled')); ");
    }
};
