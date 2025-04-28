<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Product\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\OrderController;

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');

    // Product routes
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);

    
    Route::prefix('admin')->middleware(['auth:sanctum','admin'])->group(function () {
        //Product Routes
        Route::post('/products', [ProductController::class, 'store']);
        Route::post('/product-update/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
        //Inventory routes
        Route::get('/inventory', [ProductController::class, 'inventory']);
        Route::patch('/inventory/{product}', [ProductController::class, 'adjustStock']);
        
        // Category Routes
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    });
    Route::middleware(['auth:sanctum'])->group(function () {
        // Order Routes
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
    });
});