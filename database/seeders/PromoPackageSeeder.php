<?php

namespace Database\Seeders;

use App\Models\PromoPackage;
use Illuminate\Database\Seeder;

class PromoPackageSeeder extends Seeder
{
    public function run(): void
    {
        $promoPackages = [
            [
                'name' => 'Summer Special',
                'description' => 'Get 20% off on all summer tour packages. Perfect for your beach getaway!',
                'discount_percentage' => 20.00,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'name' => 'Early Bird Discount',
                'description' => 'Book 30 days in advance and get 15% off on any tour package.',
                'discount_percentage' => 15.00,
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'name' => 'Group Travel Bonus',
                'description' => 'Traveling with 5 or more people? Get 25% discount on group bookings.',
                'discount_percentage' => 25.00,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(60),
                'is_active' => true,
            ],
            [
                'name' => 'Holiday Season Promo',
                'description' => 'Celebrate the holidays with special 30% discount on selected destinations.',
                'discount_percentage' => 30.00,
                'start_date' => now()->addDays(15),
                'end_date' => now()->addDays(45),
                'is_active' => true,
            ],
            [
                'name' => 'Weekend Warrior',
                'description' => 'Quick weekend getaway? Enjoy 10% off on 2-day tour packages.',
                'discount_percentage' => 10.00,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(20),
                'is_active' => true,
            ],
        ];

        foreach ($promoPackages as $package) {
            PromoPackage::create($package);
        }
    }
}
