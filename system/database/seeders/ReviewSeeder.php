<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            [
                'client_name' => 'John Carter',
                'company_name' => 'Demo Tech',
                'designation' => 'CEO',
                'review' => 'Their creative expertise transformed our vision into a stunning final product. The entire process was smooth, professional, and delivered beyond expectations.',
                'rating' => 5,
                'sort_order' => 1,
            ],
            [
                'client_name' => 'Sara Malik',
                'company_name' => 'Creative Labs',
                'designation' => 'Marketing Director',
                'review' => 'Communication was excellent throughout the project and the team consistently delivered on time. We saw a real improvement in our online presence.',
                'rating' => 5,
                'sort_order' => 2,
            ],
            [
                'client_name' => 'Michael Reyes',
                'company_name' => 'Northwind Retail',
                'designation' => 'Founder',
                'review' => 'A genuinely talented team that understood our brand quickly and translated it into a modern, high-converting website.',
                'rating' => 4,
                'sort_order' => 3,
            ],
        ];

        foreach ($reviews as $review) {
            Review::updateOrCreate(['client_name' => $review['client_name']], $review);
        }
    }
}
