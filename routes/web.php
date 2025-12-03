<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BloodPressureController;

Route::get('/', [BloodPressureController::class, 'showForm'])->name('bp.form');

// If someone visits /calculate with GET, just send them back to the form
Route::get('/calculate', function () {
    return redirect()->route('bp.form');
});

Route::post('/calculate', [BloodPressureController::class, 'calculate'])->name('bp.calculate');
