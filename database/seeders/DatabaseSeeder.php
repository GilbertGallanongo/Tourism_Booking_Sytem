<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Admin;
use App\Models\Destination;
use App\Models\Payment;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\FamousTouristSpot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed destinations first
        $this->call(DestinationSeeder::class);

        $adminAccount = Admin::updateOrCreate(
            ['email' => 'admin@tourph.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@tourph.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'juan@example.com'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => bcrypt('password123'),
                'role' => 'tourist',
            ]
        );

        User::updateOrCreate(
            ['email' => 'tourist@example.com'],
            [
                'name' => 'Tourist User',
                'password' => bcrypt('password123'),
                'role' => 'tourist',
            ]
        );

        $this->call(TourPackageSeeder::class);

        $this->call(FamousTouristSpotSeeder::class);

        $this->call(ReviewSeeder::class);

        // In production we skip booking seeders to avoid constraints/migration timing issues.
        if (! app()->environment('production')) {
            $this->call(BookingSeeder::class);
        }
    }
}
