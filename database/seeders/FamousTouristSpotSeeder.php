<?php

namespace Database\Seeders;

use App\Models\FamousTouristSpot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FamousTouristSpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spots = [
            [
                'name' => 'Bolinao Falls',
                'description' => 'A stunning waterfall nestled in the lush forests of Bolinao, perfect for nature lovers and adventure seekers. The falls cascade into a crystal-clear pool ideal for swimming.',
                'location' => 'Bolinao, Pangasinan',
                'image' => 'images/Bolinao Waterfall Trekking.png',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Patar White Beach',
                'description' => 'Pristine white sand beach with crystal clear waters, perfect for swimming, sunbathing, and beach activities. One of the most beautiful beaches in Northern Philippines.',
                'location' => 'Bolinao, Pangasinan',
                'image' => 'images/patar beach sunrise escape.png',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enchanted Cave',
                'description' => 'A mystical underground cave with stunning rock formations and a natural pool. A unique geological wonder that offers an unforgettable adventure experience.',
                'location' => 'Bolinao, Pangasinan',
                'image' => 'images/enchanted cave and shell museum.png',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Cape Bolinao Lighthouse',
                'description' => 'Historic lighthouse built in 1905 by the Americans, offering panoramic views of the South China Sea and the rugged coastline. A perfect spot for sunset watching.',
                'location' => 'Bolinao, Pangasinan',
                'image' => 'images/Bolinao Lighthouse Adventure.webp',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Balingasay River',
                'description' => 'Scenic river cruise through mangrove forests, offering a peaceful and eco-friendly tourism experience. Home to diverse marine life and bird species.',
                'location' => 'Bolinao, Pangasinan',
                'image' => 'images/Ecotourism & Conservation Areas.png',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Tara Falls',
                'description' => 'Hidden gem waterfall surrounded by tropical vegetation. A tranquil escape from the city, perfect for picnics and nature photography.',
                'location' => 'Bolinao, Pangasinan',
                'image' => 'images/Bolinao Waterfall Trekking.png',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($spots as $spot) {
            FamousTouristSpot::create($spot);
        }
    }
}
