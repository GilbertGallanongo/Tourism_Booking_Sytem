<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->time('time_start')->nullable()->after('duration_days');
            $table->time('time_end')->nullable()->after('time_start');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn(['time_start', 'time_end']);
        });
    }
};
