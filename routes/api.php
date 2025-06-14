<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ValidationController;


Route::prefix('validate')->group(function () {
    Route::post('/name', [ValidationController::class, 'validateName']);
    Route::post('/national-id', [ValidationController::class, 'validateNationalId']);
    Route::post('/password', [ValidationController::class, 'validatePassword']);
    Route::post('/email', [ValidationController::class, 'validateEmail']);
    Route::get('/verify-email', [ValidationController::class, 'confirmEmail']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
