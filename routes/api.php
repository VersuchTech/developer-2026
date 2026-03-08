<?php

use App\Http\Controllers\NearestGolferController;
use Illuminate\Support\Facades\Route;

Route::get('/nearest-golfers', [NearestGolferController::class, 'index']);
Route::get('/nearest-golfers/csv', [NearestGolferController::class, 'csv']);