<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            [
                'name' => 'Patar White Beach',
                'description' => 'Soft sand, gentle waves, and a dramatic coastline make Patar one of the most iconic Bolinao beaches. Perfect for swimming and sunset watching.',
                'location' => 'Bolinao, Pangasinan',
                'image' => 'images/patar-beach.png',
                'status' => 'active',
            ],
            [
                'name' => 'Enchanted Cave',
                'description' => 'Swim in crystal-clear waters and explore a hidden cave pool beneath the lush coastal cliffs. A mystical natural wonder perfect for adventurers.',
                'location' => 'Bolinao, Pangasinan',
                'image' => 'images/enchanted-cave.png',
                'status' => 'active',
            ],
            [
                'name' => 'Saint James Church',
                'description' => 'A centuries-old stone church with stunning views, rich history, and a peaceful atmosphere. A centuries-old heritage site overlooking the sea.',
                'location' => 'Bolinao, Pangasinan',
                'image' => 'images/saint-james-church.png',
                'status' => 'active',
            ],
            [
                'name' => 'Cape Bolinao Lighthouse',
                'description' => 'Iconic lighthouse with panoramic coastal views and colonial architecture. Perfect for photography and witnessing breathtaking sunsets.',
                'location' => 'Bolinao, Pangasinan',
                'image' => 'images/cape-lighthouse.png',
                'status' => 'active',
            ],
            [
                'name' => 'Hundred Islands',
                'description' => 'An archipelago of scenic islands perfect for island hopping, snorkeling, and boat tours. Discover hidden coves and pristine waters.',
                'location' => 'Alaminos, Pangasinan',
                'image' => 'images/hundred-islands.png',
                'status' => 'active',
            ],
            [
                'name' => 'Poro Point Lighthouse',
                'description' => 'Historic lighthouse with panoramic views of La Union coast. A colonial-era structure offering breathtaking seascapes.',
                'location' => 'San Fernando, La Union',
                'image' => 'images/poro-point.png',
                'status' => 'active',
            ],
        ];

        foreach ($destinations as $destination) {
            Destination::updateOrCreate(
                ['name' => $destination['name'], 'location' => $destination['location']],
                $destination
            );
        }
    }
}
