<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
  Route::get('/auth/me', [AuthController::class, 'me']);
  Route::get('/leads', [LeadController::class, 'index']);
  Route::post('/leads', [LeadController::class, 'store']);
  Route::get('/leads/{id}', [LeadController::class, 'show']);
  Route::put('/leads/{id}', [LeadController::class, 'update']);
  Route::delete('/leads/{id}', [LeadController::class, 'destroy']);
});