<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Suppliers
        |--------------------------------------------------------------------------
        */
        $suppliers = [
            [
                'name' => 'Apex Tech Distributors',
                'contact_person' => 'Rajesh Sharma',
                'email' => 'contact@apextech.in',
                'phone' => '+91 98765 43210',
                'address' => 'Plot 42, Electronic City, Bengaluru, KA',
                'payment_terms' => 'Net 30',
                'lead_time_days' => 5,
                'reliability_score' => 4.9,
                'notes' => 'Primary electronics and accessories importer.',
            ],
            [
                'name' => 'Vogue Textile Mills',
                'contact_person' => 'Pooja Mehta',
                'email' => 'sales@voguetextiles.com',
                'phone' => '+91 98251 12345',
                'address' => 'Ring Road Apparel Park, Surat, GJ',
                'payment_terms' => 'Net 15',
                'lead_time_days' => 3,
                'reliability_score' => 4.7,
                'notes' => 'Cotton and denim textile manufacturer.',
            ],
            [
                'name' => 'Heritage Book Press & Stationary',
                'contact_person' => 'Vikram Patel',
                'email' => 'orders@heritagepress.co.in',
                'phone' => '+91 98980 98765',
                'address' => 'Daryaganj Publishing House, New Delhi, DL',
                'payment_terms' => 'Net 60',
                'lead_time_days' => 7,
                'reliability_score' => 4.8,
                'notes' => 'National publisher and technical books distributor.',
            ],
            [
                'name' => 'GreenFlora Agritech & Tools',
                'contact_person' => 'Sunil Verma',
                'email' => 'supply@greenflora.org',
                'phone' => '+91 94140 55667',
                'address' => 'MIDC Industrial Area, Pune, MH',
                'payment_terms' => 'Advance',
                'lead_time_days' => 10,
                'reliability_score' => 4.5,
                'notes' => 'Manufacturer of heavy duty garden and lawn tools.',
            ],
            [
                'name' => 'Olympus Sports Equipment',
                'contact_person' => 'Amitabh Sen',
                'email' => 'support@olympussports.in',
                'phone' => '+91 99030 44556',
                'address' => 'Sports Complex Zone, Meerut, UP',
                'payment_terms' => 'Net 30',
                'lead_time_days' => 4,
                'reliability_score' => 4.9,
                'notes' => 'Professional cricket bats and FIFA quality football gear.',
            ],
        ];

        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(['email' => $s['email']], $s);
        }

        $allSuppliers = Supplier::all();

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
            Category::firstOrCreate(['name' => $category['name']], $category);
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
                'supplier_id' => $allSuppliers[0]?->id,
                'sku' => 'IPH1-9011',
                'barcode' => '890100000001',
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
                'supplier_id' => $allSuppliers[0]?->id,
                'sku' => 'LAP1-9012',
                'barcode' => '890100000002',
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
                'supplier_id' => $allSuppliers[0]?->id,
                'sku' => 'MOU1-9013',
                'barcode' => '890100000003',
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
                'supplier_id' => $allSuppliers[1]?->id,
                'sku' => 'TSH2-9021',
                'barcode' => '890100000004',
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
                'supplier_id' => $allSuppliers[1]?->id,
                'sku' => 'JEA2-9022',
                'barcode' => '890100000005',
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
                'supplier_id' => $allSuppliers[2]?->id,
                'sku' => 'LAR3-9031',
                'barcode' => '890100000006',
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
                'supplier_id' => $allSuppliers[2]?->id,
                'sku' => 'PHP3-9032',
                'barcode' => '890100000007',
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
                'supplier_id' => $allSuppliers[3]?->id,
                'sku' => 'GAR4-9041',
                'barcode' => '890100000008',
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
                'supplier_id' => $allSuppliers[4]?->id,
                'sku' => 'BAT5-9051',
                'barcode' => '890100000009',
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
                'supplier_id' => $allSuppliers[4]?->id,
                'sku' => 'BAL5-9052',
                'barcode' => '890100000010',
                'name' => 'Football',
                'description' => 'FIFA Approved',
                'price' => 1800,
                'quantity' => 0,
                'restock_level' => 5,
                'image' => null,
                'is_available' => false,
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['name' => $p['name']], $p);
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
