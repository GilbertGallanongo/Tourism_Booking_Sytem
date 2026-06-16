<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourPackage;
use App\Models\Destination;

class TourPackageSeeder extends Seeder
{
    public function run(): void
    {
        // Get destinations (they should be seeded first)
        $patar = Destination::where('name', 'Patar White Beach')->first();
        $enchanted = Destination::where('name', 'Enchanted Cave')->first();
        $stJames = Destination::where('name', 'Saint James Church')->first();
        $cape = Destination::where('name', 'Cape Bolinao Lighthouse')->first();
        $hundred = Destination::where('name', 'Hundred Islands')->first();
        $poro = Destination::where('name', 'Poro Point Lighthouse')->first();

        $packages = [
            [
                'destination_id' => $stJames?->id ?? 3,
                'name'          => 'Bolinao Church & Cape Tour',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Visit the historic Bolinao Church, Cape Bolinao Lighthouse, and the scenic seaside cliffs of the northwest coast.',
                'category'      => 'cultural',
                'price'         => 2800.00,
                'duration_days' => 1,
                'max_guests'    => 16,
                'image'         => 'images/st-james-church.jpg',
                'rating'        => 4.9,
                'status'        => 'active',
            ],
            [
                'destination_id' => $patar?->id ?? 1,
                'name'          => 'Patar Beach Sunrise Escape',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Relax on Patar Beach, swim in clear waters, and catch the sunrise over the famous white sand shore.',
                'category'      => 'natural',
                'price'         => 2600.00,
                'duration_days' => 1,
                'max_guests'    => 12,
                'image'         => 'images/patar-beach.jpg',
                'rating'        => 4.7,
                'status'        => 'active',
            ],
            [
                'destination_id' => $enchanted?->id ?? 2,
                'name'          => 'Enchanted Cave & Shell Museum',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Swim in the Enchanted Cave and explore the Shell Museum with its world-class seashell collection.',
                'category'      => 'cultural',
                'price'         => 3200.00,
                'duration_days' => 1,
                'max_guests'    => 10,
                'image'         => 'images/enchanted-cave.jpg',
                'rating'        => 4.8,
                'status'        => 'active',
            ],
            [
                'destination_id' => $cape?->id ?? 4,
                'name'          => 'Bolinao Coastal Island Hopping',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Island hopping from Bolinao beaches to nearby hidden coves with swimming and picnic stops.',
                'category'      => 'recreational',
                'price'         => 3600.00,
                'duration_days' => 1,
                'max_guests'    => 18,
                'image'         => 'images/hundred-islands.jpg',
                'rating'        => 4.8,
                'status'        => 'active',
            ],
            [
                'destination_id' => $cape?->id ?? 4,
                'name'          => 'Bolinao Lighthouse Adventure',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Explore the iconic Cape Bolinao Lighthouse with panoramic coastal views and seaside photo points.',
                'category'      => 'cultural',
                'price'         => 1900.00,
                'duration_days' => 1,
                'max_guests'    => 20,
                'image'         => 'images/cape-lighthouse.jpg',
                'rating'        => 4.6,
                'status'        => 'active',
            ],
            [
                'destination_id' => $patar?->id ?? 1,
                'name'          => 'Bolinao Waterfall Trekking',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Adventure through lush forests to discover hidden waterfalls and natural pools in Bolinao.',
                'category'      => 'natural',
                'price'         => 2200.00,
                'duration_days' => 1,
                'max_guests'    => 14,
                'image'         => 'images/waterfall-bolinao.jpg',
                'rating'        => 4.7,
                'status'        => 'active',
            ],
            [
                'destination_id' => $patar?->id ?? 1,
                'name'          => 'Coastal Village Tour & Fishing Experience',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Experience authentic local life with traditional fishing methods and visits to charming coastal villages.',
                'category'      => 'recreational',
                'price'         => 2400.00,
                'duration_days' => 1,
                'max_guests'    => 10,
                'image'         => 'images/patar-beach.jpg',
                'rating'        => 4.5,
                'status'        => 'active',
            ],
            [
                'destination_id' => $patar?->id ?? 1,
                'name'          => 'Bolinao Sunset Cruise & Dinner',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Romantic sunset cruise along the Bolinao coast with fresh seafood dinner and live entertainment.',
                'category'      => 'recreational',
                'price'         => 3800.00,
                'duration_days' => 1,
                'max_guests'    => 30,
                'image'         => 'images/patar-beach.jpg',
                'rating'        => 4.9,
                'status'        => 'active',
            ],
            [
                'destination_id' => $patar?->id ?? 1,
                'name'          => 'Bolinao Island Camping & Stargazing',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Overnight beach camping in Bolinao with bonfire, stargazing, and seaside stories.',
                'category'      => 'recreational',
                'price'         => 4500.00,
                'duration_days' => 2,
                'max_guests'    => 15,
                'image'         => 'images/hundred-islands.jpg',
                'rating'        => 4.8,
                'status'        => 'active',
            ],
            [
                'destination_id' => $enchanted?->id ?? 2,
                'name'          => 'Marine Life Discovery & Diving',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Guided snorkeling and diving tours to explore coral reefs and diverse marine life around Bolinao.',
                'category'      => 'natural',
                'price'         => 3500.00,
                'duration_days' => 1,
                'max_guests'    => 12,
                'image'         => 'images/enchanted-cave.jpg',
                'rating'        => 4.7,
                'status'        => 'active',
            ],
            [
                'destination_id' => $stJames?->id ?? 3,
                'name'          => 'Heritage & Culture Museum Tour',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Deep dive into Bolinao\'s rich history with guided tours of museums and historical landmarks.',
                'category'      => 'cultural',
                'price'         => 1600.00,
                'duration_days' => 1,
                'max_guests'    => 25,
                'image'         => 'images/st-james-church.jpg',
                'rating'        => 4.4,
                'status'        => 'active',
            ],
            [
                'destination_id' => $patar?->id ?? 1,
                'name'          => 'Wellness Retreat & Beach Spa',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Relaxing spa treatments, yoga sessions, and wellness activities on the beach with ocean views.',
                'category'      => 'accommodation',
                'price'         => 3900.00,
                'duration_days' => 1,
                'max_guests'    => 20,
                'image'         => 'images/patar-beach.jpg',
                'rating'        => 4.8,
                'status'        => 'active',
            ],
            [
                'destination_id' => $patar?->id ?? 1,
                'name'          => 'Patar Beach Family Picnic',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'A family-friendly beach day with shaded picnic setup, swimming time, local snacks, and photo stops along Patar.',
                'category'      => 'natural',
                'price'         => 3000.00,
                'duration_days' => 1,
                'max_guests'    => 18,
                'image'         => 'images/patar-beach.jpg',
                'rating'        => 4.6,
                'status'        => 'active',
            ],
            [
                'destination_id' => $stJames?->id ?? 3,
                'name'          => 'Bolinao Heritage Food Walk',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Taste local Bolinao favorites while visiting heritage streets, town landmarks, and community food stops.',
                'category'      => 'cultural',
                'price'         => 2100.00,
                'duration_days' => 1,
                'max_guests'    => 14,
                'image'         => 'images/st-james-church.jpg',
                'rating'        => 4.5,
                'status'        => 'active',
            ],
            [
                'destination_id' => $cape?->id ?? 4,
                'name'          => 'Bolinao Mangrove Eco Cruise',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Cruise through quiet mangrove areas with a local guide, birdwatching stops, and coastal conservation stories.',
                'category'      => 'ecotourism',
                'price'         => 2700.00,
                'duration_days' => 1,
                'max_guests'    => 16,
                'image'         => 'images/hundred-islands.jpg',
                'rating'        => 4.6,
                'status'        => 'active',
            ],
        ];

        foreach ($packages as $package) {
            TourPackage::updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }
    }
}
