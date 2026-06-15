<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Auth Routes

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
