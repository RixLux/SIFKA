<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kebersihan',
                'icon_marker' => 'trash-2',
                'color_code' => '#10b981', // Green
            ],
            [
                'name' => 'Kelistrikan',
                'icon_marker' => 'zap',
                'color_code' => '#f59e0b', // Yellow/Amber
            ],
            [
                'name' => 'Infrastruktur',
                'icon_marker' => 'hammer',
                'color_code' => '#ef4444', // Red
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
