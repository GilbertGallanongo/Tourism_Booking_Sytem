<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('tourist_guide')->default(false)->after('additional_fees');
            $table->decimal('tourist_guide_fee', 12, 2)->default(0)->after('tourist_guide');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['tourist_guide', 'tourist_guide_fee']);
        });
    }
};
