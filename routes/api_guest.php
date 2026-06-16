<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

/**
 * Auth Routes
 */
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);

/**
 * Store Routes
 */
Route::get('stores/{store}', [StoreController::class, 'show']);

/**
 * Product Category Routes
 */
Route::get('product-categories/{productCategory}/products', [ProductCategoryController::class, 'index']);

/**
 * Home Page Routes
 */
Route::get('home-page', [HomePageController::class, 'index']);
