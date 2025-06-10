<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Order_ItemController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptchaController;

//Auth

Route::post('/login', [AuthController::class, 'login']);
Route::get('/captcha', [CaptchaController::class, 'reload']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Product Routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{id}', [ProductController::class, 'update']);
    Route::post('/products/{id}/delete', [ProductController::class, 'destroy']);

    // Category Routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::post('/categories/{id}', [CategoryController::class, 'update']);
    Route::post('/categories/{id}/delete', [CategoryController::class, 'destroy']);

    // Order Item Routes
    Route::get('/order-items', [Order_ItemController::class, 'index']);
    Route::get('/order-items/{id}', [Order_ItemController::class, 'show']);
    Route::post('/order-items', [Order_ItemController::class, 'store']);
    Route::post('/order-items/{id}', [Order_ItemController::class, 'update']);
    Route::post('/order-items/{id}/delete', [Order_ItemController::class, 'destroy']);

    // Order Routes
    Route::get('/orders', [OrdersController::class, 'index']);
    Route::get('/orders/{id}', [OrdersController::class, 'show']);
    Route::post('/orders', [OrdersController::class, 'store']);
    Route::post('/orders/{id}', [OrdersController::class, 'update']);
    Route::post('/orders/{id}/delete', [OrdersController::class, 'destroy']);
});