<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'available_products' => Product::where('is_available', true)->count(),
            'low_stock_products' => Product::where('quantity', '<', 10)->where('quantity', '>', 0)->count(),
            'out_of_stock' => Product::where('quantity', 0)->count(),
            'total_value' => Product::sum('price'),
        ];

        $recent_products = Product::with('category')
            ->latest()
            ->take(5)
            ->get();

        $top_categories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();

        return view('backpack::dashboard', compact('stats', 'recent_products', 'top_categories'));
    }
}