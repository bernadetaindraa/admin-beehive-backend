<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ArticleCategory;

class ArticleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Drone Application',
            'Technology & Innovation',
            'Partnership & Collaboration',
            'Events & Projects',
            'Company News',
            'Product & Services',
            'Impact & Sustainability',
        ];

        foreach ($categories as $name) {
            ArticleCategory::create(['name' => $name]);
        }
    }
}
