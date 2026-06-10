<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'check_in_date')) {
                $table->renameColumn('check_in_date', 'tour_start_date');
            }
            if (Schema::hasColumn('bookings', 'check_out_date')) {
                $table->renameColumn('check_out_date', 'tour_end_date');
            }
            if (Schema::hasColumn('bookings', 'check_in_at')) {
                $table->renameColumn('check_in_at', 'tour_started_at');
            }
            if (Schema::hasColumn('bookings', 'check_out_at')) {
                $table->renameColumn('check_out_at', 'tour_ended_at');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'tour_start_date')) {
                $table->renameColumn('tour_start_date', 'check_in_date');
            }
            if (Schema::hasColumn('bookings', 'tour_end_date')) {
                $table->renameColumn('tour_end_date', 'check_out_date');
            }
            if (Schema::hasColumn('bookings', 'tour_started_at')) {
                $table->renameColumn('tour_started_at', 'check_in_at');
            }
            if (Schema::hasColumn('bookings', 'tour_ended_at')) {
                $table->renameColumn('tour_ended_at', 'check_out_at');
            }
        });
    }
};
