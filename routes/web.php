<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BloodPressureController;

Route::get('/', [BloodPressureController::class, 'showForm'])->name('bp.form');
Route::post('/calculate', [BloodPressureController::class, 'calculate'])->name('bp.calculate');
/*
Route::get('/', function () {
    return view('welcome');
});
*/
