<?php

use App\Http\Controllers\Admin\AuditLogCrudController;
use App\Http\Controllers\Admin\CategoryCrudController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductCrudController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\SupplierCrudController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', ['web']),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
], function () {

    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::crud('category', CategoryCrudController::class);

    Route::crud('supplier', SupplierCrudController::class);

    Route::crud('product', ProductCrudController::class);

    Route::crud('audit-log', AuditLogCrudController::class);

    Route::post('product/{id}/quick-restock', [ProductCrudController::class, 'quickRestock'])
        ->name('product.quick.restock');

    Route::get('product/export/csv', [ProductCrudController::class, 'exportCsv'])
        ->name('product.export.csv');

    Route::get('product/{id}/preview', [ProductCrudController::class, 'preview'])
        ->name('product.preview');

    Route::get('product/barcode-sheet/{id?}', [ProductCrudController::class, 'barcodeSheet'])
        ->name('product.barcode.sheet');

    Route::get('purchase-order/draft', [PurchaseOrderController::class, 'generate'])
        ->name('admin.purchase-order.generate');

    Route::get('purchase-order/csv', [PurchaseOrderController::class, 'exportCsv'])
        ->name('admin.purchase-order.csv');
});
