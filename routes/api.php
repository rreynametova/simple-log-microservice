<?php
use Illuminate\Support\Facades\Route;

// Log recording route
Route::post('/logs', [\App\Http\Controllers\LogController::class, 'store'])->name('logs.store');
