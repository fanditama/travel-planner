<?php
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'register']);