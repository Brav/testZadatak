<?php

use Illuminate\Support\Facades\Route;

Route::get('/items/{categoryID?}', [App\Http\Controllers\TVController::class, 'index']);

Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index']);
