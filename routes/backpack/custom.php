<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryCrudController;
use App\Http\Controllers\Admin\ProductCrudController;

// Custom dashboard route
Route::get('dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

// CRUD routes with full namespace
Route::crud('category', CategoryCrudController::class);
Route::crud('product', ProductCrudController::class);