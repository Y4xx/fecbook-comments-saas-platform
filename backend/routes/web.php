<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FacebookAuthController;
use App\Http\Controllers\FacebookPageController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect('/dashboard');
});

// Facebook OAuth routes
Route::get('/auth/facebook/redirect', [FacebookAuthController::class, 'redirect'])->name('facebook.redirect');
Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'callback'])->name('facebook.callback');

// Protected routes with Inertia
Route::middleware(['auth:sanctum'])->group(function () {
    // Facebook page selection and connection
    Route::get('/facebook/select-page', [FacebookAuthController::class, 'selectPage'])->name('facebook.select');
    Route::post('/facebook/pages/connect', [FacebookAuthController::class, 'connectPage'])->name('facebook.connect');
    
    // Dashboard pages
    Route::get('/dashboard', [FacebookPageController::class, 'index'])->name('dashboard');
    Route::get('/pages', [FacebookPageController::class, 'index'])->name('pages.index');
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});

// Auth pages (handled by Inertia)
Route::get('/login', function () {
    return inertia('Auth/Login');
})->name('login');

Route::get('/register', function () {
    return inertia('Auth/Register');
})->name('register');

