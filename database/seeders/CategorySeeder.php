<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and gadgets',
                'children' => [
                    'Smartphones',
                    'Laptops',
                    'Tablets',
                    'Headphones'
                ]
            ],
            [
                'name' => 'Fashion',
                'description' => 'Clothing and accessories',
                'children' => [
                    'Men\'s Clothing',
                    'Women\'s Clothing',
                    'Shoes',
                    'Accessories'
                ]
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home improvement and garden supplies',
                'children' => [
                    'Furniture',
                    'Kitchen',
                    'Garden Tools',
                    'Home Decor'
                ]
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'children' => [
                    'Fitness Equipment',
                    'Outdoor Gear',
                    'Sports Apparel',
                    'Team Sports'
                ]
            ],
            [
                'name' => 'Books & Media',
                'description' => 'Books, movies, and media content',
                'children' => [
                    'Books',
                    'Movies & TV',
                    'Music',
                    'Games'
                ]
            ]
        ];

        foreach ($categories as $index => $categoryData) {
            // Create parent category
            $parentCategory = Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'is_active' => true,
                'sort_order' => $index + 1,
                'parent_id' => null
            ]);

            // Create child categories
            foreach ($categoryData['children'] as $childIndex => $childName) {
                Category::create([
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'description' => "Products in {$childName} category",
                    'is_active' => true,
                    'sort_order' => $childIndex + 1,
                    'parent_id' => $parentCategory->id
                ]);
            }
        }

        $this->command->info('Categories created successfully!');
    }
}
