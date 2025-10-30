<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CareerController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/categories', [ArticleController::class, 'categories']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::post('/articles/{id}', [ArticleController::class, 'update']);
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);

    Route::get('/careers', [CareerController::class, 'index']);
    Route::post('/careers', [CareerController::class, 'store']);
    Route::post('/careers/{id}', [CareerController::class, 'update']);
    Route::delete('/careers/{id}', [CareerController::class, 'destroy']);
});
