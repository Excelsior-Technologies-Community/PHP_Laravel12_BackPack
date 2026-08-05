<?php

use App\Http\Controllers\Admin\CategoryCrudController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductCrudController;
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

    Route::crud('product', ProductCrudController::class);

    Route::get('product/export/csv', [ProductCrudController::class, 'exportCsv'])
        ->name('product.export.csv');

    Route::get('product/{id}/preview', [ProductCrudController::class, 'preview'])
        ->name('product.preview');
});
