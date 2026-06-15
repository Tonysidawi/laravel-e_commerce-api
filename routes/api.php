<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreController;

// User Routes
Route::get('user', [UserController::class, 'show']);
Route::post('users/{user}', [UserController::class, 'update']);
Route::delete('users/{user}', [UserController::class, 'destroy']);

// Store Routes
Route::get('stores', [StoreController::class, 'index']);
Route::post('stores', [StoreController::class, 'store']);
Route::get('stores/{store}', [StoreController::class, 'show']);
Route::put('stores/{store}', [StoreController::class, 'update']);
Route::delete('stores/{store}', [StoreController::class, 'destroy']);
