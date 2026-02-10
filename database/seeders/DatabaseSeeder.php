<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories', 'is_active' => true],
            ['name' => 'Clothing', 'description' => 'Fashion and apparel', 'is_active' => true],
            ['name' => 'Books', 'description' => 'Books and stationery', 'is_active' => true],
            ['name' => 'Home & Garden', 'description' => 'Home improvement and garden supplies', 'is_active' => true],
            ['name' => 'Sports', 'description' => 'Sports equipment and gear', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create products
        $products = [
            [
                'category_id' => 1,
                'name' => 'Smartphone X',
                'description' => 'Latest smartphone with advanced features',
                'price' => 699.99,
                'quantity' => 50,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Wireless Headphones',
                'description' => 'Noise-cancelling wireless headphones',
                'price' => 199.99,
                'quantity' => 30,
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'T-Shirt',
                'description' => 'Cotton t-shirt for daily wear',
                'price' => 19.99,
                'quantity' => 100,
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Programming Book',
                'description' => 'Learn programming with this comprehensive guide',
                'price' => 39.99,
                'quantity' => 25,
                'is_available' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Gardening Tool Set',
                'description' => 'Complete set for garden maintenance',
                'price' => 49.99,
                'quantity' => 15,
                'is_available' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create admin user
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}