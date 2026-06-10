<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Confirmation & Reference
            $table->string('confirmation_code')->nullable()->unique()->after('booking_number');
            $table->string('reference_code')->nullable()->unique()->after('confirmation_code');
            
            // Booking Details
            $table->integer('num_adults')->default(0)->after('num_guests');
            $table->integer('num_children')->default(0)->after('num_adults');
            $table->integer('num_seniors')->default(0)->after('num_children');
            
            // Pricing Breakdown
            $table->decimal('base_price', 12, 2)->nullable()->after('total_price');
            $table->decimal('additional_fees', 12, 2)->default(0)->after('base_price');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('additional_fees');
            $table->string('discount_code')->nullable()->after('discount_amount');
            
            // Booking Status
            $table->text('cancellation_reason')->nullable()->after('special_requests');
            $table->decimal('refund_amount', 12, 2)->nullable()->after('cancellation_reason');
            $table->timestamp('cancelled_at')->nullable()->after('refund_amount');
            
            // Timeline
            $table->timestamp('confirmed_at')->nullable()->after('cancelled_at');
            $table->timestamp('completed_at')->nullable()->after('confirmed_at');
            
            // Payment Plan
            $table->string('payment_plan')->default('full')->after('completed_at'); // full, installment
            $table->integer('payment_installments')->default(1)->after('payment_plan');
            
            // Additional Info
            $table->json('guest_details')->nullable()->after('payment_installments');
            $table->json('services')->nullable()->after('guest_details');
            $table->text('internal_notes')->nullable()->after('services');
            $table->text('admin_notes')->nullable()->after('internal_notes');
            
            // Tracking
            $table->boolean('reminder_sent')->default(false)->after('admin_notes');
            $table->timestamp('reminder_sent_at')->nullable()->after('reminder_sent');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'confirmation_code',
                'reference_code',
                'num_adults',
                'num_children',
                'num_seniors',
                'base_price',
                'additional_fees',
                'discount_amount',
                'discount_code',
                'cancellation_reason',
                'refund_amount',
                'cancelled_at',
                'confirmed_at',
                'completed_at',
                'payment_plan',
                'payment_installments',
                'guest_details',
                'services',
                'internal_notes',
                'admin_notes',
                'reminder_sent',
                'reminder_sent_at',
            ]);
        });
    }
};
