<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/product', [ProductController::class, 'index']);
    Route::get('/product/{product}', [ProductController::class, 'showById']);
    Route::post('/product', [ProductController::class, 'store']);
    Route::put('/product/{product}/update-estoque', [ProductController::class, 'updateEstoque']);
    Route::delete('/product/{product}', [ProductController::class, 'destroy']);
});

