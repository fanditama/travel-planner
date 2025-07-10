<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware(['api.auth'])->group(function () {
	// Route User
	Route::get('/users/current', [UserController::class, 'get']);
	Route::put('/users/current', [UserController::class, 'update']);
});
