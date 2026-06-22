<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('throttle:auth');
    Route::get('/user', [AuthController::class, 'me']);

    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('buildings', BuildingController::class);
    Route::apiResource('facilities', FacilityController::class);

    Route::apiResource('reports', ReportController::class);
    Route::post('/reports/{report}/seen', [ReportController::class, 'markAsSeen']);
    Route::apiResource('users', UserController::class);

    Broadcast::routes();
});
