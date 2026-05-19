<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TrackerController;
use App\Http\Controllers\Api\CryptoPriceController;

Route::get('/prices', [CryptoPriceController::class, 'index']);
Route::post('/visit', [TrackerController::class, 'store']);
