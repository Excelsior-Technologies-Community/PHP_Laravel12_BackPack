<?php

use App\Http\Controllers\Admin\DashboardController;

// Admin authentication routes
Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () {
    
    // Dashboard route
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // CRUD routes
    Route::crud('category', 'CategoryCrudController');
    Route::crud('product', 'ProductCrudController');
    
});