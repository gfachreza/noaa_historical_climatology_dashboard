<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

Route::get('/weather', [WeatherController::class, 'index']);
Route::get('/meta/countries', [WeatherController::class, 'countries']);
Route::get('/meta/date-range', [WeatherController::class, 'dateRange']);
Route::get('/compare', [WeatherController::class, 'compare']);
