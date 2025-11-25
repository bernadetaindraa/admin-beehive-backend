<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicProjectController;
use App\Http\Controllers\PublicCareerController;
use App\Http\Controllers\PublicArticleController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Articles Routes
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/categories', [ArticleController::class, 'categories']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::put('/articles/{id}', [ArticleController::class, 'update']);
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);

    // Careers Routes
    Route::get('/careers', [CareerController::class, 'index']);
    Route::post('/careers', [CareerController::class, 'store']);
    Route::put('/careers/{id}', [CareerController::class, 'update']);
    Route::delete('/careers/{id}', [CareerController::class, 'destroy']);

    // Projects Routes
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/dropdowns', [ProjectController::class, 'dropdowns']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);

    // Products Routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
});

Route::prefix('public')->group(function () {
    //Public Articles Routes
    Route::get('/articles', [PublicArticleController::class, 'index']);
    Route::get('/articles/{id}', [PublicArticleController::class, 'show']);

    //Public Projects Routes
    Route::get('/projects', [PublicProjectController::class, 'index']);
    Route::get('/projects/{id}', [PublicProjectController::class, 'detail']);
    Route::get('/categories/product-services', function () {
        return \App\Models\ProductService::select('id', 'name')->orderBy('name')->get();
    });
    Route::get('/categories/industries', function () {
        return \App\Models\Industry::select('id', 'name')->orderBy('name')->get();
    });

    //Public Careers Routes
    Route::get('/careers', [PublicCareerController::class, 'index']);
    Route::get('/careers/{id}', [PublicCareerController::class, 'show']);
});