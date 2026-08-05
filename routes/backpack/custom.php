<?php

<<<<<<< HEAD
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
    
=======
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

>>>>>>> stages
});