<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaseOrderController extends Controller
{
    public function generate(Request $request)
    {
        $supplierId = $request->get('supplier_id');
        $query = Product::with(['category', 'supplier'])
            ->whereColumn('quantity', '<=', 'restock_level');

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $lowStockProducts = $query->orderBy('quantity')->get();

        // Calculate recommendations
        $poItems = $lowStockProducts->map(function ($product) {
            $deficiency = max(0, ($product->restock_level * 2) - $product->quantity);
            $recommendedQty = max($deficiency, 5);
            $estimatedCost = round($product->price * 0.70, 2); // 70% wholesale cost
            $subtotal = $recommendedQty * $estimatedCost;

            return (object) [
                'product' => $product,
                'current_stock' => $product->quantity,
                'restock_level' => $product->restock_level,
                'recommended_qty' => $recommendedQty,
                'unit_cost' => $estimatedCost,
                'subtotal' => $subtotal,
                'supplier' => $product->supplier,
            ];
        });

        $totalEstimatedCost = $poItems->sum('subtotal');
        $totalUnitsToOrder = $poItems->sum('recommended_qty');
        $poNumber = 'PO-' . date('Ymd') . '-' . strtoupper(substr(md5(microtime()), 0, 4));
        $poDate = date('d M, Y');
        $suppliers = Supplier::all();
        $selectedSupplier = $supplierId ? Supplier::find($supplierId) : null;

        return view('admin.purchase-order', compact(
            'poItems',
            'totalEstimatedCost',
            'totalUnitsToOrder',
            'poNumber',
            'poDate',
            'suppliers',
            'selectedSupplier'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $supplierId = $request->get('supplier_id');
        $query = Product::with(['category', 'supplier'])
            ->whereColumn('quantity', '<=', 'restock_level');

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $lowStockProducts = $query->orderBy('quantity')->get();
        $poNumber = 'PO-' . date('Ymd') . '-' . strtoupper(substr(md5(microtime()), 0, 4));

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $poNumber . '.csv',
        ];

        $callback = function () use ($lowStockProducts, $poNumber) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['PURCHASE ORDER: ' . $poNumber, 'Date: ' . date('Y-m-d')]);
            fputcsv($file, []);
            fputcsv($file, [
                'Item #',
                'SKU',
                'Product Name',
                'Category',
                'Preferred Supplier',
                'Current Stock',
                'Restock Threshold',
                'Recommended Order Qty',
                'Estimated Unit Cost (₹)',
                'Line Total (₹)',
            ]);

            $i = 1;
            $grandTotal = 0;
            foreach ($lowStockProducts as $p) {
                $deficiency = max(0, ($p->restock_level * 2) - $p->quantity);
                $recommendedQty = max($deficiency, 5);
                $unitCost = round($p->price * 0.70, 2);
                $lineTotal = $recommendedQty * $unitCost;
                $grandTotal += $lineTotal;

                fputcsv($file, [
                    $i++,
                    $p->sku ?? 'N/A',
                    $p->name,
                    $p->category?->name ?? 'N/A',
                    $p->supplier?->name ?? 'Unassigned',
                    $p->quantity,
                    $p->restock_level,
                    $recommendedQty,
                    $unitCost,
                    $lineTotal,
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', '', '', '', 'GRAND TOTAL:', '', $grandTotal]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
