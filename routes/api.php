<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('blogs', BlogController::class);
Route::apiResource('categories', CategoryController::class);
