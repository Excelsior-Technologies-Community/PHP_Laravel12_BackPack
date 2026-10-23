<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Supplier;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Setup CRUD
     */
    public function setup()
    {
        CRUD::setModel(Product::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product');
        CRUD::setEntityNameStrings('Product', 'Products');

        $this->crud->setDefaultPageLength(10);
        $this->crud->setPageLengthMenu([10, 25, 50, 100]);
        $this->crud->orderBy('id', 'ASC');
    }

    /**
     * List Operation
     */
    protected function setupListOperation()
    {
        // Add Quick Restock Modal Widget into the list page
        Widget::add()->type('view')->view('admin.widgets.quick_restock_modal');

        CRUD::column('id')->label('ID');

        CRUD::column('image')
            ->type('image')
            ->disk('public')
            ->height('50px')
            ->width('50px')
            ->label('Image');

        CRUD::column('sku')
            ->label('SKU')
            ->type('closure')
            ->function(function ($entry) {
                return '<code>' . e($entry->sku ?? 'N/A') . '</code>';
            })
            ->escaped(false);

        CRUD::column('name')
            ->label('Product Name')
            ->wrapper([
                'element' => 'strong',
            ]);

        CRUD::column('category')
            ->type('relationship')
            ->attribute('name')
            ->label('Category');

        CRUD::column('supplier')
            ->type('relationship')
            ->attribute('name')
            ->label('Supplier')
            ->default('<span class="text-muted">None</span>');

        CRUD::column('price')
            ->type('number')
            ->prefix('₹')
            ->decimals(2);

        CRUD::column('quantity');

        CRUD::column('restock_level')
            ->label('Restock Level');

        CRUD::addColumn([
            'name' => 'stock_status',
            'label' => 'Stock Status',
            'type' => 'closure',
            'escaped' => false,
            'function' => function ($entry) {
                if ($entry->quantity == 0) {
                    return '<span class="badge bg-danger">Out Of Stock</span>';
                } elseif ($entry->quantity <= $entry->restock_level) {
                    return '<span class="badge bg-warning text-dark">Low Stock</span>';
                }
                return '<span class="badge bg-success">In Stock</span>';
            }
        ]);

        CRUD::column('is_available')
            ->type('boolean')
            ->label('Available');

        /*
        |--------------------------------------------------------------------------
        | Buttons
        |--------------------------------------------------------------------------
        */
        $this->crud->addButtonFromView('line', 'preview', 'preview_product', 'beginning');
        $this->crud->addButtonFromView('line', 'quick_restock', 'quick_restock', 'end');
        $this->crud->addButtonFromView('line', 'barcode_sheet', 'barcode_sheet_button', 'end');
    }

    /**
     * Create Operation
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:2|max:255',
            'sku' => 'nullable|max:50|unique:products,sku,' . ($this->crud->getCurrentEntryId() ?? 'NULL'),
            'barcode' => 'nullable|max:50',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'restock_level' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Tab: Basic Details
        CRUD::field('name')
            ->label('Product Name')
            ->type('text')
            ->tab('Basic Details');

        CRUD::field('sku')
            ->label('SKU (Stock Keeping Unit - Auto Generated if empty)')
            ->type('text')
            ->tab('Basic Details');

        CRUD::field('barcode')
            ->label('Barcode Number (Auto Generated if empty)')
            ->type('text')
            ->tab('Basic Details');

        CRUD::field([
            'name' => 'category_id',
            'label' => 'Category',
            'type' => 'select',
            'entity' => 'category',
            'model' => Category::class,
            'attribute' => 'name',
            'tab' => 'Basic Details'
        ]);

        CRUD::field([
            'name' => 'supplier_id',
            'label' => 'Primary Supplier / Vendor',
            'type' => 'select',
            'entity' => 'supplier',
            'model' => Supplier::class,
            'attribute' => 'name',
            'tab' => 'Basic Details'
        ]);

        CRUD::field('description')
            ->label('Description')
            ->type('textarea')
            ->tab('Basic Details');

        // Tab: Inventory & Pricing
        CRUD::field('price')
            ->label('Retail Price (₹)')
            ->type('number')
            ->attributes(['step' => '0.01', 'min' => 0])
            ->tab('Inventory & Pricing');

        CRUD::field('quantity')
            ->label('Stock Quantity')
            ->type('number')
            ->tab('Inventory & Pricing');

        CRUD::field('restock_level')
            ->label('Low-Stock Restock Threshold')
            ->type('number')
            ->default(10)
            ->tab('Inventory & Pricing');

        // Tab: Media & Status
        CRUD::field([
            'name' => 'image',
            'label' => 'Product Image',
            'type' => 'upload',
            'withFiles' => [
                'disk' => 'public',
                'path' => 'products'
            ],
            'tab' => 'Media & Status'
        ]);

        CRUD::field('is_available')
            ->label('Available for Orders')
            ->type('checkbox')
            ->default(true)
            ->tab('Media & Status');
    }

    /**
     * Update Operation
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        $this->crud->addButtonFromView('line', 'preview', 'preview_product', 'beginning');
        $this->crud->addButtonFromView('line', 'quick_restock', 'quick_restock', 'end');
        $this->crud->addButtonFromView('line', 'barcode_sheet', 'barcode_sheet_button', 'end');
    }

    /**
     * Show Operation
     */
    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        CRUD::column('id')->label('Product ID');
        CRUD::column('sku')->label('SKU Code');
        CRUD::column('barcode')->label('Barcode');
        CRUD::column('name')->label('Product Name');
        CRUD::column('category')->type('relationship')->attribute('name')->label('Category');
        CRUD::column('supplier')->type('relationship')->attribute('name')->label('Supplier');
        CRUD::column('price')->type('number')->prefix('₹')->decimals(2);
        CRUD::column('quantity')->label('Current Stock');
        CRUD::column('restock_level')->label('Restock Threshold');

        CRUD::addColumn([
            'name' => 'stock_status',
            'label' => 'Stock Status',
            'type' => 'closure',
            'escaped' => false,
            'function' => function ($entry) {
                if ($entry->quantity == 0) {
                    return '<span class="badge bg-danger">Out Of Stock</span>';
                } elseif ($entry->quantity <= $entry->restock_level) {
                    return '<span class="badge bg-warning text-dark">Low Stock</span>';
                }
                return '<span class="badge bg-success">In Stock</span>';
            }
        ]);

        CRUD::column('description')->label('Description');

        CRUD::addColumn([
            'name' => 'recent_audit_history',
            'label' => 'Recent Activity Audit Timeline',
            'type' => 'closure',
            'escaped' => false,
            'function' => function ($entry) {
                $logs = $entry->auditLogs()->take(5)->get();
                if ($logs->isEmpty()) {
                    return '<span class="text-muted">No historical changes logged yet.</span>';
                }

                $html = '<ul class="list-group list-group-flush small">';
                foreach ($logs as $log) {
                    $actionBadge = match ($log->action) {
                        'CREATED' => 'bg-success',
                        'UPDATED' => 'bg-primary',
                        'STOCK_ADJUSTED' => 'bg-warning text-dark',
                        'DELETED' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                    $html .= '<li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <span class="badge ' . $actionBadge . ' me-2">' . e($log->action) . '</span>
                            <span>' . e($log->description ?? 'Activity logged') . '</span>
                            <span class="text-muted ms-2">(by ' . e($log->user_name ?? 'System') . ')</span>
                        </div>
                        <span class="text-muted">' . $log->created_at->diffForHumans() . '</span>
                    </li>';
                }
                $html .= '</ul>';
                return $html;
            }
        ]);
    }

    /**
     * Quick Stock Adjustment
     */
    public function quickRestock(Request $request, $id)
    {
        $request->validate([
            'adjustment_type' => 'required|in:stock_in,stock_out,damaged,manual_override',
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($id);
        $before = $product->quantity;
        $type = $request->adjustment_type;
        $inputQty = (int) $request->quantity;
        $reason = $request->reason ?: 'Quick manual restock adjustment';

        if ($type === 'stock_in') {
            $change = $inputQty;
            $after = $before + $inputQty;
        } elseif ($type === 'stock_out') {
            $change = -$inputQty;
            $after = max(0, $before - $inputQty);
        } elseif ($type === 'damaged') {
            $change = -$inputQty;
            $after = max(0, $before - $inputQty);
        } else { // manual_override
            $after = max(0, $inputQty);
            $change = $after - $before;
        }

        $product->quantity = $after;
        if ($after > 0) {
            $product->is_available = true;
        }
        $product->save();

        // Record Stock Adjustment
        StockAdjustment::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => $type,
            'quantity_before' => $before,
            'quantity_change' => $change,
            'quantity_after' => $after,
            'reason' => $reason,
        ]);

        // Record in Audit Log
        AuditLog::record(
            'STOCK_ADJUSTED',
            $product,
            ['quantity' => $before],
            ['quantity' => $after, 'adjustment_type' => $type, 'delta' => $change],
            sprintf('Stock adjusted (%s: %s%d). Reason: %s', strtoupper($type), $change >= 0 ? '+' : '', $change, $reason)
        );

        \Alert::success(sprintf('Stock for "%s" adjusted from %d to %d units.', $product->name, $before, $after))->flash();

        return redirect()->back();
    }

    /**
     * Printable Barcode & QR Label Sheet
     */
    public function barcodeSheet(Request $request, $id = null)
    {
        if ($id) {
            $products = Product::with(['category', 'supplier'])->where('id', $id)->get();
        } else {
            $products = Product::with(['category', 'supplier'])->orderBy('name')->get();
        }

        $format = $request->get('format', 'thermal'); // 'thermal' or 'a4'

        return view('admin.barcode-print-sheet', compact('products', 'format', 'id'));
    }

    /**
     * Product Preview
     */
    public function preview($id)
    {
        $product = Product::with(['category', 'supplier', 'auditLogs', 'stockAdjustments'])->findOrFail($id);

        return view('admin.product-preview', compact('product'));
    }

    /**
     * Export Products CSV
     */
    public function exportCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=products.csv',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'SKU',
                'Barcode',
                'Name',
                'Category',
                'Supplier',
                'Price (INR)',
                'Quantity',
                'Restock Level',
                'Available',
            ]);

            Product::with(['category', 'supplier'])->get()->each(function ($product) use ($file) {
                fputcsv($file, [
                    $product->id,
                    $product->sku,
                    $product->barcode,
                    $product->name,
                    optional($product->category)->name ?? 'N/A',
                    optional($product->supplier)->name ?? 'N/A',
                    $product->price,
                    $product->quantity,
                    $product->restock_level,
                    $product->is_available ? 'Yes' : 'No',
                ]);
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
