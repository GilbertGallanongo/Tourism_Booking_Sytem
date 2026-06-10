<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Bookings indexes
        $this->createIndexIfNotExists('bookings', ['status', 'created_at'], 'bookings_status_created_at_index');
        $this->createIndexIfNotExists('bookings', ['user_id', 'status'], 'bookings_user_id_status_index');
        $this->createIndexIfNotExists('bookings', ['tour_package_id', 'status'], 'bookings_tour_package_id_status_index');
        $this->createIndexIfNotExists('bookings', ['tour_start_date'], 'bookings_tour_start_date_index');
        $this->createIndexIfNotExists('bookings', ['tour_end_date'], 'bookings_tour_end_date_index');
        $this->createIndexIfNotExists('bookings', ['tour_date'], 'bookings_tour_date_index');

        // Tour packages indexes
        $this->createIndexIfNotExists('tour_packages', ['status', 'rating'], 'tour_packages_status_rating_index');
        $this->createIndexIfNotExists('tour_packages', ['location'], 'tour_packages_location_index');
        $this->createIndexIfNotExists('tour_packages', ['category'], 'tour_packages_category_index');
        $this->createIndexIfNotExists('tour_packages', ['destination_id'], 'tour_packages_destination_id_index');
        $this->createIndexIfNotExists('tour_packages', ['status', 'created_at'], 'tour_packages_status_created_at_index');

        // Reviews indexes
        $this->createIndexIfNotExists('reviews', ['tour_package_id', 'rating'], 'reviews_tour_package_id_rating_index');
        $this->createIndexIfNotExists('reviews', ['user_id', 'tour_package_id'], 'reviews_user_id_tour_package_id_index');
        $this->createIndexIfNotExists('reviews', ['created_at'], 'reviews_created_at_index');

        // Payments indexes
        $this->createIndexIfNotExists('payments', ['booking_id', 'status'], 'payments_booking_id_status_index');
        $this->createIndexIfNotExists('payments', ['status'], 'payments_status_index');
    }

    private function createIndexIfNotExists($table, $columns, $indexName): void
    {
        $schema = DB::getSchemaBuilder();
        $connection = DB::connection();
        
        // Check if index exists (SQLite specific)
        if ($connection->getDriverName() === 'sqlite') {
            $indexes = DB::select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name='" . $table . "' AND name='" . $indexName . "'");
            if (empty($indexes)) {
                Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        } else {
            // For MySQL/PostgreSQL
            try {
                Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            } catch (\Exception $e) {
                // Index already exists, ignore
            }
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_status_created_at_index');
            $table->dropIndex('bookings_user_id_status_index');
            $table->dropIndex('bookings_tour_package_id_status_index');
            $table->dropIndex('bookings_tour_start_date_index');
            $table->dropIndex('bookings_tour_end_date_index');
            $table->dropIndex('bookings_tour_date_index');
        });

        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropIndex('tour_packages_status_rating_index');
            $table->dropIndex('tour_packages_location_index');
            $table->dropIndex('tour_packages_category_index');
            $table->dropIndex('tour_packages_destination_id_index');
            $table->dropIndex('tour_packages_status_created_at_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_tour_package_id_rating_index');
            $table->dropIndex('reviews_user_id_tour_package_id_index');
            $table->dropIndex('reviews_created_at_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_booking_id_status_index');
            $table->dropIndex('payments_status_index');
        });
    }
};
