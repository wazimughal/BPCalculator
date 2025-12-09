<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BloodPressureController;
use Illuminate\Support\Facades\Artisan;

// Show the BP form on GET /
Route::get('/', [BloodPressureController::class, 'showForm'])->name('bp.form');

// Handle the BP calculation on POST / (same URL, different method)
Route::post('/', [BloodPressureController::class, 'calculate'])->name('bp.calculate');

// If someone tries GET /calculate, just send them back to the form
Route::get('/calculate', function () {
    return redirect()->route('bp.form');
});

// To clear all the caches
Route::get('/clear-all', function () {
    Artisan::call('optimize:clear');
    return 'All caches cleared!';
});
