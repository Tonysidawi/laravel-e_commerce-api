<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * User Routes
 */
Route::get('user', [UserController::class, 'show']);
Route::post('users/{user}', [UserController::class, 'update']);
Route::delete('users/{user}', [UserController::class, 'destroy']);

/**
 * Store Routes
 */
Route::get('stores', [StoreController::class, 'index']);
Route::post('stores', [StoreController::class, 'store']);
Route::get('stores/{store}', [StoreController::class, 'show']);
Route::put('stores/{store}', [StoreController::class, 'update']);
Route::delete('stores/{store}', [StoreController::class, 'destroy']);

/**
 * Product Routes
 */
Route::apiResource('products', ProductController::class);

/**
 * Image Routes
 */
Route::post('images/{image}/main', [ImageController::class, 'setAsMain']);
Route::post('images', [ImageController::class, 'store']);
Route::delete('images/{image}', [ImageController::class, 'destroy']);
