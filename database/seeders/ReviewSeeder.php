<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\TourPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = [
            [
                'user_id' => 2,
                'tour_package_id' => 1,
                'rating' => 5,
                'comment' => 'Absolutely amazing experience! The Bolinao Church was breathtaking and the lighthouse offered stunning views. Our guide was knowledgeable and friendly. Highly recommend!',
            ],
            [
                'user_id' => 3,
                'tour_package_id' => 8,
                'rating' => 5,
                'comment' => 'The sunset cruise was magical! The fresh seafood dinner was delicious and the live entertainment was wonderful. Perfect romantic getaway. Worth every peso!',
            ],
            [
                'user_id' => 2,
                'tour_package_id' => 3,
                'rating' => 4,
                'comment' => 'Great tour! The Enchanted Cave was fascinating and swimming in the underground pool was refreshing. The Shell Museum had incredible displays. Highly enjoyed!',
            ],
            [
                'user_id' => 3,
                'tour_package_id' => 4,
                'rating' => 5,
                'comment' => 'Island hopping at Hundred Islands was the highlight of our trip! Crystal clear waters, beautiful beaches, and the boat crew was excellent. Can\'t wait to return!',
            ],
            [
                'user_id' => 2,
                'tour_package_id' => 5,
                'rating' => 4,
                'comment' => 'Poro Point Lighthouse adventure was incredible. The coastal views were spectacular and the hike was well-organized. A bit tiring but totally worth it!',
            ],
            [
                'user_id' => 3,
                'tour_package_id' => 6,
                'rating' => 5,
                'comment' => 'The waterfall trekking experience was unforgettable! Nature at its finest. Our guide was amazing and made sure everyone stayed safe. Perfect adventure!',
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
