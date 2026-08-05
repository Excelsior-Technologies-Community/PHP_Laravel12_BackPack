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

            'low_stock_products' => Product::whereColumn('quantity', '<=', 'restock_level')
                ->where('quantity', '>', 0)
                ->count(),

            'out_of_stock' => Product::where('quantity', 0)->count(),

            'total_stock_quantity' => Product::sum('quantity'),

            // Total Inventory Value
            'total_inventory_value' => Product::selectRaw('SUM(price * quantity) as total')
                ->value('total') ?? 0,

            // Average Product Price
            'average_price' => Product::avg('price') ?? 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | Dashboard Data
        |--------------------------------------------------------------------------
        */

        $recent_products = Product::with('category')
            ->latest()
            ->take(5)
            ->get();

        $top_categories = Category::withCount('products')
            ->orderByDesc('products_count')
            ->take(5)
            ->get();

        $highest_price_product = Product::orderByDesc('price')->first();

        $lowest_price_product = Product::orderBy('price')->first();

        /*
        |--------------------------------------------------------------------------
        | Inventory Dashboard
        |--------------------------------------------------------------------------
        */

        $low_stock_products = Product::with('category')
            ->whereColumn('quantity', '<=', 'restock_level')
            ->where('quantity', '>', 0)
            ->orderBy('quantity')
            ->take(5)
            ->get();

        $out_of_stock_products = Product::with('category')
            ->where('quantity', 0)
            ->latest()
            ->take(5)
            ->get();

        $valuable_products = Product::with('category')
            ->orderByRaw('(price * quantity) DESC')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Category Analytics
        |--------------------------------------------------------------------------
        */

        $category_analytics = Category::withCount('products')
            ->with('products')
            ->get()
            ->map(function ($category) {

                $category->inventory_value = $category->products->sum(function ($product) {
                    return $product->price * $product->quantity;
                });

                $category->stock_quantity = $category->products->sum('quantity');

                return $category;
            })
            ->sortByDesc('inventory_value')
            ->values();

        return view('admin.dashboard', compact(
            'stats',
            'recent_products',
            'top_categories',
            'highest_price_product',
            'lowest_price_product',
            'low_stock_products',
            'out_of_stock_products',
            'valuable_products',
            'category_analytics'
        ));
    }
}
