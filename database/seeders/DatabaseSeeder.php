<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'is_active' => true,
            ],
            [
                'name' => 'Clothing',
                'description' => 'Fashion and apparel',
                'is_active' => true,
            ],
            [
                'name' => 'Books',
                'description' => 'Books and stationery',
                'is_active' => true,
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home improvement products',
                'is_active' => true,
            ],
            [
                'name' => 'Sports',
                'description' => 'Sports equipment',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        $products = [

            // Electronics
            [
                'category_id' => 1,
                'name' => 'iPhone 16',
                'description' => 'Apple Smartphone',
                'price' => 85000,
                'quantity' => 25,
                'restock_level' => 10,
                'image' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Gaming Laptop',
                'description' => 'High-end gaming laptop',
                'price' => 120000,
                'quantity' => 5,
                'restock_level' => 10,
                'image' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Wireless Mouse',
                'description' => 'Bluetooth Mouse',
                'price' => 1500,
                'quantity' => 0,
                'restock_level' => 5,
                'image' => null,
                'is_available' => false,
            ],

            // Clothing
            [
                'category_id' => 2,
                'name' => 'Cotton T-Shirt',
                'description' => 'Premium Cotton',
                'price' => 799,
                'quantity' => 80,
                'restock_level' => 20,
                'image' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Denim Jeans',
                'description' => 'Slim Fit',
                'price' => 1999,
                'quantity' => 8,
                'restock_level' => 15,
                'image' => null,
                'is_available' => true,
            ],

            // Books
            [
                'category_id' => 3,
                'name' => 'Laravel 12 Guide',
                'description' => 'Complete Laravel Book',
                'price' => 999,
                'quantity' => 50,
                'restock_level' => 10,
                'image' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'PHP Cookbook',
                'description' => 'Advanced PHP',
                'price' => 1200,
                'quantity' => 2,
                'restock_level' => 10,
                'image' => null,
                'is_available' => true,
            ],

            // Home & Garden
            [
                'category_id' => 4,
                'name' => 'Garden Tool Kit',
                'description' => 'Complete Tool Kit',
                'price' => 2500,
                'quantity' => 18,
                'restock_level' => 5,
                'image' => null,
                'is_available' => true,
            ],

            // Sports
            [
                'category_id' => 5,
                'name' => 'Cricket Bat',
                'description' => 'English Willow',
                'price' => 4500,
                'quantity' => 12,
                'restock_level' => 5,
                'image' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 5,
                'name' => 'Football',
                'description' => 'FIFA Approved',
                'price' => 1800,
                'quantity' => 0,
                'restock_level' => 5,
                'image' => null,
                'is_available' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        /*
        |--------------------------------------------------------------------------
        | Admin User
        |--------------------------------------------------------------------------
        */

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
    }
}