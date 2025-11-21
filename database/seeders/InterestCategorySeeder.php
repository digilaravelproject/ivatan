<?php

namespace Database\Seeders;

use App\Models\Interest;
use App\Models\InterestCategory;
use Illuminate\Database\Seeder;

class InterestCategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // 1. Technology
            'Technology' => [
                'Web Development',
                'Software Engineering',
                'AI / Machine Learning',
                'Mobile Apps',
                'Cybersecurity',
                'Gaming / eSports',
            ],

            // 2. Business & Finance
            'Business & Finance' => [
                'Startups & Entrepreneurship',
                'Investing & Stock Market',
                'Personal Finance',
                'Real Estate',
                'Marketing & Sales',
                'Crypto / Blockchain',
            ],

            // 3. Jobs & Careers
            'Jobs & Careers' => [
                'Quick Hiring / Part-time',
                'Freelancing / Remote Work',
                'Corporate Jobs',
                'Government Jobs',
                'Skill Development',
            ],

            // 4. Education & Learning
            'Education & Learning' => [
                'Coding / IT',
                'Business & Management',
                'Language Learning',
                'Competitive Exams',
                'Science & Research',
            ],

            // 5. Entertainment & Lifestyle
            'Entertainment & Lifestyle' => [
                'Movies & Series',
                'Music',
            ],
        ];

        foreach ($data as $categoryName => $interests) {

            // Create Category
            $category = InterestCategory::create([
                'name' => $categoryName
            ]);

            // Create Interests under this category
            foreach ($interests as $item) {
                Interest::create([
                    'name' => $item,
                    'interest_category_id' => $category->id,
                    'description' => null
                ]);
            }
        }
    }
}
