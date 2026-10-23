<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\AuditLog;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers = Supplier::count();

        $healthyCount = Product::whereColumn('quantity', '>', 'restock_level')->count();
        $lowStockCount = Product::whereColumn('quantity', '<=', 'restock_level')
            ->where('quantity', '>', 0)
            ->count();
        $outOfStockCount = Product::where('quantity', 0)->count();

        $totalInventoryValue = Product::selectRaw('SUM(price * quantity) as total')->value('total') ?? 0;
        $totalStockQuantity = Product::sum('quantity') ?? 0;
        $averagePrice = Product::avg('price') ?? 0;

        // Estimated Wholesale Procurement Cost (~70% of retail)
        $estimatedProcurementCost = $totalInventoryValue * 0.70;
        $projectedGrossMargin = $totalInventoryValue - $estimatedProcurementCost;
        $stockHealthScore = $totalProducts > 0 ? round(($healthyCount / $totalProducts) * 100, 1) : 100;

        $stats = [
            'total_products' => $totalProducts,
            'total_categories' => $totalCategories,
            'total_suppliers' => $totalSuppliers,
            'available_products' => Product::where('is_available', true)->count(),
            'healthy_products' => $healthyCount,
            'low_stock_products' => $lowStockCount,
            'out_of_stock' => $outOfStockCount,
            'total_stock_quantity' => $totalStockQuantity,
            'total_inventory_value' => $totalInventoryValue,
            'average_price' => $averagePrice,
            'procurement_cost' => $estimatedProcurementCost,
            'gross_margin' => $projectedGrossMargin,
            'stock_health_score' => $stockHealthScore,
        ];

        /*
        |--------------------------------------------------------------------------
        | Chart 1: Category Asset Breakdown (Donut Chart)
        |--------------------------------------------------------------------------
        */
        $categoryBreakdown = Category::with('products')->get()->map(function ($cat) {
            $val = $cat->products->sum(fn($p) => $p->price * $p->quantity);
            return [
                'name' => $cat->name,
                'value' => round($val, 2),
                'count' => $cat->products->count(),
            ];
        })->sortByDesc('value')->values();

        $chartCategoryLabels = $categoryBreakdown->pluck('name')->toArray();
        $chartCategoryValues = $categoryBreakdown->pluck('value')->toArray();

        /*
        |--------------------------------------------------------------------------
        | Chart 2: Top 5 High-Value Inventory Lines (Horizontal Bar Chart)
        |--------------------------------------------------------------------------
        */
        $topValuableProducts = Product::selectRaw('id, name, price, quantity, (price * quantity) as total_val')
            ->orderByDesc('total_val')
            ->take(5)
            ->get();

        $chartTopProductLabels = $topValuableProducts->pluck('name')->toArray();
        $chartTopProductValues = $topValuableProducts->pluck('total_val')->toArray();

        /*
        |--------------------------------------------------------------------------
        | Chart 3: Stock Health Distribution (Doughnut Chart)
        |--------------------------------------------------------------------------
        */
        $chartStockHealthLabels = ['Healthy Stock', 'Low Stock', 'Out of Stock'];
        $chartStockHealthValues = [$healthyCount, $lowStockCount, $outOfStockCount];

        /*
        |--------------------------------------------------------------------------
        | Automated Reorder Watchdog: Products requiring Restock
        |--------------------------------------------------------------------------
        */
        $watchdogProducts = Product::with(['category', 'supplier'])
            ->whereColumn('quantity', '<=', 'restock_level')
            ->orderBy('quantity')
            ->get()
            ->map(function ($p) {
                $deficit = max(0, ($p->restock_level * 2) - $p->quantity);
                $p->recommended_order = max($deficit, 5);
                $p->estimated_restock_cost = round($p->recommended_order * ($p->price * 0.7), 2);
                return $p;
            });

        /*
        |--------------------------------------------------------------------------
        | Recent Activity Audit Logs
        |--------------------------------------------------------------------------
        */
        $recentAuditLogs = AuditLog::latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'stats',
            'chartCategoryLabels',
            'chartCategoryValues',
            'chartTopProductLabels',
            'chartTopProductValues',
            'chartStockHealthLabels',
            'chartStockHealthValues',
            'watchdogProducts',
            'recentAuditLogs'
        ));
    }
}
