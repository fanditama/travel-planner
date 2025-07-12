<?php

use App\Http\Controllers\DestinationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

// Route Destination
Route::post('/destinations', [DestinationController::class, 'create']);
Route::get('/destinations/{id}', [DestinationController::class, 'get'])->where('id', '[0-9]+');
Route::put('/destinations/{id}', [DestinationController::class, 'update'])->where('id', '[0-9]+');
Route::delete('/destinations/{id}', [DestinationController::class, 'delete'])->where('id', '[0-9]+');

Route::middleware(['api.auth'])->group(function () {
	// Route User
	Route::get('/users/current', [UserController::class, 'get']);
	Route::put('/users/current', [UserController::class, 'update']);
	Route::delete('/users/logout', [UserController::class, 'logout']);
});
