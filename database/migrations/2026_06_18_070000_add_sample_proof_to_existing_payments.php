<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payments')
            ->where('status', 'paid')
            ->where(function ($query) {
                $query->whereNull('proof')
                    ->orWhereRaw("TRIM(proof) = ''");
            })
            ->update([
                'proof' => 'images/sample-proof-of-payment.png',
            ]);
    }

    public function down(): void
    {
        DB::table('payments')
            ->where('proof', 'images/sample-proof-of-payment.png')
            ->update([
                'proof' => null,
            ]);
    }
};
