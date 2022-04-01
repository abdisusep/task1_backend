<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProvinceController;

Route::resource('/province', ProvinceController::class)->except(['create', 'edit']);
Route::get('/province/search/{value}', [ProvinceController::class, 'search']);
