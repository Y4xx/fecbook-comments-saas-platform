<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FacebookPageController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Facebook Pages
    Route::get('/pages', [FacebookPageController::class, 'index']);
    Route::get('/pages/{page}', [FacebookPageController::class, 'show']);
    Route::post('/pages/{page}/toggle', [FacebookPageController::class, 'toggleActive']);
    Route::delete('/pages/{page}', [FacebookPageController::class, 'destroy']);
    
    // Comments
    Route::get('/comments', [CommentController::class, 'index']);
    Route::get('/comments/{comment}', [CommentController::class, 'show']);
    
    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index']);
});
