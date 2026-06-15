<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GisMapController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\WebAuthController;

// Auth Routes
Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Protected Admin Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    });

    Route::get('/analytics', [AnalyticsController::class, 'index']);

    Route::get('/gis', [GisMapController::class, 'index']);
    Route::post('/gis/tj-box', [GisMapController::class, 'saveTjBox']);
    Route::post('/gis/fiber-line', [GisMapController::class, 'saveFiberLine']);

    Route::get('/customers', [\App\Http\Controllers\CustomerWebController::class, 'index']);
    Route::get('/routers', [\App\Http\Controllers\RouterWebController::class, 'index']);
    Route::get('/billing', [\App\Http\Controllers\BillingWebController::class, 'index']);
});
