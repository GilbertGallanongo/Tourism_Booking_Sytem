<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_histories', function (Blueprint $table) {
            $table->id();
            $table->string('format', 10);
            $table->string('filename');
            $table->string('path');
            $table->unsignedInteger('row_count')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->string('generated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_histories');
    }
};
