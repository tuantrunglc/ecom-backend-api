<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::role('admin')->first();
        
        $products = [
            // Electronics - Smartphones
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Latest iPhone with advanced camera system and A17 Pro chip',
                'short_description' => 'Premium smartphone with cutting-edge technology',
                'price' => 999.00,
                'sale_price' => 899.00,
                'stock_quantity' => 50,
                'category' => 'Smartphones',
                'featured' => true
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'description' => 'Flagship Android phone with AI-powered features',
                'short_description' => 'Advanced Android smartphone',
                'price' => 849.00,
                'stock_quantity' => 75,
                'category' => 'Smartphones',
                'featured' => true
            ],
            
            // Electronics - Laptops
            [
                'name' => 'MacBook Pro 16"',
                'description' => 'Professional laptop with M3 Pro chip for demanding tasks',
                'short_description' => 'High-performance laptop for professionals',
                'price' => 2499.00,
                'stock_quantity' => 25,
                'category' => 'Laptops',
                'featured' => true
            ],
            [
                'name' => 'Dell XPS 13',
                'description' => 'Ultra-portable laptop with stunning display',
                'short_description' => 'Compact and powerful ultrabook',
                'price' => 1299.00,
                'sale_price' => 1199.00,
                'stock_quantity' => 40,
                'category' => 'Laptops'
            ],
            
            // Fashion - Men's Clothing
            [
                'name' => 'Classic Cotton T-Shirt',
                'description' => 'Comfortable 100% cotton t-shirt in various colors',
                'short_description' => 'Essential cotton t-shirt',
                'price' => 29.99,
                'stock_quantity' => 200,
                'category' => 'Men\'s Clothing'
            ],
            [
                'name' => 'Denim Jeans',
                'description' => 'Premium denim jeans with modern fit',
                'short_description' => 'Stylish denim jeans',
                'price' => 89.99,
                'sale_price' => 69.99,
                'stock_quantity' => 150,
                'category' => 'Men\'s Clothing'
            ],
            
            // Fashion - Women's Clothing
            [
                'name' => 'Summer Dress',
                'description' => 'Light and airy summer dress perfect for warm weather',
                'short_description' => 'Elegant summer dress',
                'price' => 79.99,
                'stock_quantity' => 100,
                'category' => 'Women\'s Clothing',
                'featured' => true
            ],
            
            // Home & Garden - Furniture
            [
                'name' => 'Modern Office Chair',
                'description' => 'Ergonomic office chair with lumbar support',
                'short_description' => 'Comfortable ergonomic chair',
                'price' => 299.99,
                'sale_price' => 249.99,
                'stock_quantity' => 30,
                'category' => 'Furniture'
            ],
            
            // Sports & Outdoors - Fitness Equipment
            [
                'name' => 'Yoga Mat Premium',
                'description' => 'Non-slip yoga mat with extra cushioning',
                'short_description' => 'Professional yoga mat',
                'price' => 49.99,
                'stock_quantity' => 80,
                'category' => 'Fitness Equipment'
            ],
            
            // Books & Media - Books
            [
                'name' => 'The Art of Programming',
                'description' => 'Comprehensive guide to modern programming practices',
                'short_description' => 'Essential programming book',
                'price' => 59.99,
                'stock_quantity' => 60,
                'category' => 'Books'
            ]
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            
            if ($category) {
                Product::create([
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'description' => $productData['description'],
                    'short_description' => $productData['short_description'],
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'price' => $productData['price'],
                    'sale_price' => $productData['sale_price'] ?? null,
                    'stock_quantity' => $productData['stock_quantity'],
                    'manage_stock' => true,
                    'in_stock' => true,
                    'status' => 'active',
                    'featured' => $productData['featured'] ?? false,
                    'category_id' => $category->id,
                    'created_by' => $admin->id,
                    'weight' => rand(100, 5000) / 100, // Random weight between 1-50kg
                    'images' => [
                        'https://via.placeholder.com/800x600?text=' . urlencode($productData['name']),
                        'https://via.placeholder.com/800x600?text=' . urlencode($productData['name'] . '+2')
                    ]
                ]);
            }
        }

        $this->command->info('Products created successfully!');
    }
}
